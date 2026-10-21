<?php

namespace App\Services;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\LogEmprestimo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmprestimoService
{
    /**
     * @param  array{funcionario_id:int, chave_id:int, data_retirada?:string|null, hora_retirada?:string|null, data_devolucao?:string|null, hora_devolucao?:string|null, status?:string}  $attributes
     */
    public function create(array $attributes): Emprestimo
    {
        return DB::transaction(function () use ($attributes): Emprestimo {
            $chave = Chave::query()
                ->lockForUpdate()
                ->findOrFail($attributes['chave_id']);

            $this->ensureAvailable($chave);

            $emprestimo = Emprestimo::create([
                ...$attributes,
                'status' => 'Em andamento',
            ]);

            $chave->update(['status' => 'Emprestada']);

            LogEmprestimo::create([
                'emprestimo_id' => $emprestimo->getKey(),
                'acao' => 'Empréstimo registrado',
                'data_hora' => now(),
            ]);

            return $emprestimo->load(['funcionario', 'chave']);
        }, attempts: 3);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Emprestimo $emprestimo, array $attributes): Emprestimo
    {
        return DB::transaction(function () use ($emprestimo, $attributes): Emprestimo {
            $registro = Emprestimo::query()
                ->lockForUpdate()
                ->findOrFail($emprestimo->getKey());

            $previousKeyId = (int) $registro->chave_id;
            $nextKeyId = (int) ($attributes['chave_id'] ?? $previousKeyId);
            $wasActive = $this->isActive($registro->status);
            $willBeActive = $this->isActive($attributes['status'] ?? $registro->status);

            if ($previousKeyId !== $nextKeyId && $wasActive) {
                throw ValidationException::withMessages([
                    'chave_id' => 'Não é possível trocar a chave de um empréstimo em andamento.',
                ]);
            }

            $chave = Chave::query()
                ->whereKey($nextKeyId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($willBeActive && ! $wasActive) {
                $this->ensureAvailable($chave);
            }

            $registro->fill($attributes)->save();

            if ($willBeActive) {
                $chave->update(['status' => 'Emprestada']);
            } elseif ($wasActive) {
                $this->releaseIfUnused($chave, (int) $registro->getKey());
            }

            LogEmprestimo::create([
                'emprestimo_id' => $registro->getKey(),
                'acao' => 'Empréstimo atualizado para '.$registro->status,
                'data_hora' => now(),
            ]);

            return $registro->refresh()->load(['funcionario', 'chave']);
        }, attempts: 3);
    }

    public function delete(Emprestimo $emprestimo): void
    {
        DB::transaction(function () use ($emprestimo): void {
            $registro = Emprestimo::query()
                ->lockForUpdate()
                ->findOrFail($emprestimo->getKey());
            $wasActive = $this->isActive($registro->status);
            $chave = Chave::query()
                ->whereKey($registro->chave_id)
                ->lockForUpdate()
                ->firstOrFail();

            $registro->delete();

            if ($wasActive) {
                $this->releaseIfUnused($chave, (int) $registro->getKey());
            }
        }, attempts: 3);
    }

    private function ensureAvailable(Chave $chave): void
    {
        $hasActiveEmprestimo = $chave->emprestimos()
            ->whereIn('status', ['Em andamento', 'Atrasado'])
            ->exists();

        if ($chave->status !== 'Disponível' || $hasActiveEmprestimo) {
            throw ValidationException::withMessages([
                'chave_id' => 'A chave selecionada não está disponível para empréstimo.',
            ]);
        }
    }

    private function isActive(string $status): bool
    {
        return in_array($status, ['Em andamento', 'Atrasado'], true);
    }

    private function releaseIfUnused(Chave $chave, int $emprestimoId): void
    {
        $hasOtherActiveEmprestimo = $chave->emprestimos()
            ->where('id', '!=', $emprestimoId)
            ->whereIn('status', ['Em andamento', 'Atrasado'])
            ->exists();

        if (! $hasOtherActiveEmprestimo) {
            $chave->update(['status' => 'Disponível']);
        }
    }
}
