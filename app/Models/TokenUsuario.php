<?php

namespace App\Models;

use Database\Factories\TokenUsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenUsuario extends Model
{
    /** @use HasFactory<TokenUsuarioFactory> */
    use HasFactory;

    protected $connection = 'mysql_laravel';

    protected $table = 'token_usuario';

    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'usuario_id',
        'token',
        'valido_ate',
    ];

    protected function casts(): array
    {
        return [
            'valido_ate' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
