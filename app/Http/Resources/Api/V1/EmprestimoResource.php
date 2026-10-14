<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmprestimoResource extends JsonResource
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
            'funcionario_id' => $this->funcionario_id,
            'chave_id' => $this->chave_id,
            'data_retirada' => $this->data_retirada,
            'hora_retirada' => $this->hora_retirada,
            'data_devolucao' => $this->data_devolucao,
            'hora_devolucao' => $this->hora_devolucao,
            'status' => $this->status,
        ];
    }
}
