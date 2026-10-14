<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

#[Hidden(['senha'])]
class Usuario extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    protected $connection = 'mysql_laravel';

    protected $table = 'usuario';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'cpf',
        'data_nascimento',
    ];

    public function retiradas(): HasMany
    {
        return $this->hasMany(Retirada::class);
    }

    public function tokensUsuario(): HasMany
    {
        return $this->hasMany(TokenUsuario::class);
    }
}
