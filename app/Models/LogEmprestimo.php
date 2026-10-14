<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEmprestimo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'log_emprestimo';

    protected $fillable = [
        'emprestimo_id',
        'acao',
        'data_hora',
    ];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
        ];
    }

    public function emprestimo(): BelongsTo
    {
        return $this->belongsTo(Emprestimo::class);
    }
}
