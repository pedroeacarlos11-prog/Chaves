<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogEmprestimoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'emprestimo_id' => $this->emprestimo_id,
            'acao' => $this->acao,
            'data_hora' => $this->data_hora?->toISOString(),
        ];
    }
}
