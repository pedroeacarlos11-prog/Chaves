<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emprestimo extends Model
{
    use HasFactory;

    protected $table = 'emprestimo';

    public $timestamps = false;

    protected $fillable = [
        'funcionario_id',
        'chave_id',
        'data_retirada',
        'hora_retirada',
        'data_devolucao',
        'hora_devolucao',
        'status',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function chave(): BelongsTo
    {
        return $this->belongsTo(Chave::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogEmprestimo::class);
    }
}
