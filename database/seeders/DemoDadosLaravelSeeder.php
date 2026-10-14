<?php

namespace Database\Seeders;

use App\Models\Retirada;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDadosLaravelSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'SenhaDemo123!';

    public function run(): void
    {
        $demoUsers = [
            [
                'nome' => 'Weslley',
                'email' => 'demo.weslley@example.test',
                'cpf' => '111.111.111-11',
                'data_nascimento' => '2002-05-12',
                'bloco' => 'Bloco A',
                'sala' => '101',
                'hora_retirada' => '08:00:00',
                'hora_devolucao' => null,
                'status' => 'em_uso',
            ],
            [
                'nome' => 'Leonardo',
                'email' => 'demo.leonardo@example.test',
                'cpf' => '222.222.222-22',
                'data_nascimento' => '2001-03-22',
                'bloco' => 'Bloco B',
                'sala' => '202',
                'hora_retirada' => '08:30:00',
                'hora_devolucao' => '09:15:00',
                'status' => 'devolvida',
            ],
            [
                'nome' => 'Guilherme',
                'email' => 'demo.guilherme@example.test',
                'cpf' => '333.333.333-33',
                'data_nascimento' => '2000-07-08',
                'bloco' => 'Bloco C',
                'sala' => '303',
                'hora_retirada' => '09:00:00',
                'hora_devolucao' => null,
                'status' => 'em_uso',
            ],
            [
                'nome' => 'Gabriel',
                'email' => 'demo.gabriel@example.test',
                'cpf' => '444.444.444-44',
                'data_nascimento' => '2003-11-19',
                'bloco' => 'Bloco A',
                'sala' => '104',
                'hora_retirada' => '09:30:00',
                'hora_devolucao' => '10:00:00',
                'status' => 'devolvida',
            ],
            [
                'nome' => 'Pedro Henrique',
                'email' => 'demo.pedrohenrique@example.test',
                'cpf' => '555.555.555-55',
                'data_nascimento' => '2001-01-25',
                'bloco' => 'Bloco B',
                'sala' => '205',
                'hora_retirada' => '10:00:00',
                'hora_devolucao' => null,
                'status' => 'em_uso',
            ],
        ];

        foreach ($demoUsers as $demoUser) {
            $usuario = Usuario::query()->firstOrCreate(
                ['email' => $demoUser['email']],
                [
                    'nome' => $demoUser['nome'],
                    'senha' => Hash::make(self::DEMO_PASSWORD),
                    'cpf' => $demoUser['cpf'],
                    'data_nascimento' => $demoUser['data_nascimento'],
                ],
            );

            Retirada::query()->firstOrCreate(
                [
                    'usuario_id' => $usuario->getKey(),
                    'data_retirada' => '2026-10-07',
                    'hora_retirada' => $demoUser['hora_retirada'],
                ],
                [
                    'bloco' => $demoUser['bloco'],
                    'sala' => $demoUser['sala'],
                    'hora_devolucao' => $demoUser['hora_devolucao'],
                    'status' => $demoUser['status'],
                ],
            );
        }
    }
}
