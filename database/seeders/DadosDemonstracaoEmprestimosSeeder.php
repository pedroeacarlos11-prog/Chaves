<?php

namespace Database\Seeders;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use App\Models\LogEmprestimo;
use App\Services\EmprestimoService;
use Illuminate\Database\Seeder;

class DadosDemonstracaoEmprestimosSeeder extends Seeder
{
    /**
     * Seed the application with repeatable example employees, keys, loans, and loan history.
     */
    public function run(EmprestimoService $emprestimoService): void
    {
        $funcionarios = [];

        foreach ([
            ['nome' => 'Ana Martins (Demonstração)', 'email' => 'ana.martins.demo@example.test', 'cargo' => 'Instrutora'],
            ['nome' => 'Bruno Costa (Demonstração)', 'email' => 'bruno.costa.demo@example.test', 'cargo' => 'Técnico de laboratório'],
            ['nome' => 'Carla Nunes (Demonstração)', 'email' => 'carla.nunes.demo@example.test', 'cargo' => 'Coordenadora'],
        ] as $dadosFuncionario) {
            $funcionarios[$dadosFuncionario['email']] = Funcionario::query()->firstOrCreate(
                ['email' => $dadosFuncionario['email']],
                [
                    'nome' => $dadosFuncionario['nome'],
                    'cargo' => $dadosFuncionario['cargo'],
                ],
            );
        }

        $chaves = [];

        foreach ([
            ['nome_chave' => 'DEMO - Laboratório 01', 'localizacao' => 'Bloco A - Laboratório de Informática'],
            ['nome_chave' => 'DEMO - Sala de Redes', 'localizacao' => 'Bloco B - Sala 202'],
            ['nome_chave' => 'DEMO - Almoxarifado', 'localizacao' => 'Bloco C - Almoxarifado'],
            ['nome_chave' => 'DEMO - Sala de Reunião', 'localizacao' => 'Bloco A - Sala 104'],
        ] as $dadosChave) {
            $chaves[$dadosChave['nome_chave']] = Chave::query()->firstOrCreate(
                ['nome_chave' => $dadosChave['nome_chave']],
                [
                    'localizacao' => $dadosChave['localizacao'],
                    'status' => 'Disponível',
                ],
            );
        }

        foreach ([
            [
                'funcionario_email' => 'ana.martins.demo@example.test',
                'chave_nome' => 'DEMO - Laboratório 01',
                'data_retirada' => '2026-10-19',
                'hora_retirada' => '08:00:00',
                'status' => 'Em andamento',
                'logs' => [
                    ['acao' => 'Empréstimo de demonstração registrado', 'data_hora' => '2026-10-19 08:00:00'],
                ],
            ],
            [
                'funcionario_email' => 'bruno.costa.demo@example.test',
                'chave_nome' => 'DEMO - Sala de Redes',
                'data_retirada' => '2026-10-10',
                'hora_retirada' => '09:00:00',
                'status' => 'Atrasado',
                'logs' => [
                    ['acao' => 'Empréstimo de demonstração registrado', 'data_hora' => '2026-10-10 09:00:00'],
                ],
            ],
            [
                'funcionario_email' => 'carla.nunes.demo@example.test',
                'chave_nome' => 'DEMO - Almoxarifado',
                'data_retirada' => '2026-10-15',
                'hora_retirada' => '14:00:00',
                'status' => 'Devolvido',
                'data_devolucao' => '2026-10-15',
                'hora_devolucao' => '16:10:00',
                'logs' => [
                    ['acao' => 'Empréstimo de demonstração registrado', 'data_hora' => '2026-10-15 14:00:00'],
                    ['acao' => 'Devolução de demonstração registrada', 'data_hora' => '2026-10-15 16:10:00'],
                ],
            ],
        ] as $dadosEmprestimo) {
            $funcionario = $funcionarios[$dadosEmprestimo['funcionario_email']];
            $chave = $chaves[$dadosEmprestimo['chave_nome']];

            $emprestimo = Emprestimo::query()
                ->where('funcionario_id', $funcionario->getKey())
                ->where('chave_id', $chave->getKey())
                ->where('data_retirada', $dadosEmprestimo['data_retirada'])
                ->where('hora_retirada', $dadosEmprestimo['hora_retirada'])
                ->first();

            $foiCriadoAgora = false;

            if (! $emprestimo && $chave->status === 'Disponível') {
                $emprestimo = $emprestimoService->create([
                    'funcionario_id' => $funcionario->getKey(),
                    'chave_id' => $chave->getKey(),
                    'data_retirada' => $dadosEmprestimo['data_retirada'],
                    'hora_retirada' => $dadosEmprestimo['hora_retirada'],
                ]);
                $foiCriadoAgora = true;
            }

            if (! $emprestimo) {
                continue;
            }

            if ($foiCriadoAgora && $dadosEmprestimo['status'] !== 'Em andamento') {
                $emprestimo = $emprestimoService->update($emprestimo, [
                    'status' => $dadosEmprestimo['status'],
                    'data_devolucao' => $dadosEmprestimo['data_devolucao'] ?? null,
                    'hora_devolucao' => $dadosEmprestimo['hora_devolucao'] ?? null,
                ]);
            }

            foreach ($dadosEmprestimo['logs'] as $dadosLog) {
                LogEmprestimo::query()->firstOrCreate([
                    'emprestimo_id' => $emprestimo->getKey(),
                    'acao' => $dadosLog['acao'],
                    'data_hora' => $dadosLog['data_hora'],
                ]);
            }
        }
    }
}
