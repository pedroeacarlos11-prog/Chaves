<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chave extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'chave';

    protected $attributes = [
        'status' => 'Disponível',
    ];

    protected $fillable = [
        'nome_chave',
        'localizacao',
        'status',
    ];

    public function emprestimos(): HasMany
    {
        return $this->hasMany(Emprestimo::class);
    }
}
