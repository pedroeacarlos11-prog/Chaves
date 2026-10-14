<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionario';

    protected $fillable = [
        'nome',
        'email',
        'cargo',
    ];

    public function emprestimos(): HasMany
    {
        return $this->hasMany(Emprestimo::class);
    }
}
