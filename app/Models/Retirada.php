<?php

namespace App\Models;

use Database\Factories\RetiradaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retirada extends Model
{
    /** @use HasFactory<RetiradaFactory> */
    use HasFactory;

    protected $connection = 'mysql_laravel';

    protected $table = 'retiradas';

    protected $attributes = [
        'status' => 'em_uso',
    ];

    protected $fillable = [
        'usuario_id',
        'bloco',
        'sala',
        'data_retirada',
        'hora_retirada',
        'hora_devolucao',
        'status',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
