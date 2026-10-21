<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function salvar_funcionario(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|',
            'cargo' => 'required|string|',
        ]);

        try {
            $funcionario = new Funcionario;
            $funcionario->nome = $request->nome;
            $funcionario->email = $request->email;
            $funcionario->cargo = $request->cargo;

            $funcionario->save();

            return response()->json(['message' => 'Funcionário cadastrado com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar funcionário!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function ver_funcionario(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $funcionario = Funcionario::find($request->id);
        if ($funcionario) {
            return response()->json(['funcionario' => $funcionario, 'erro' => 'n'], 200);
        } else {
            return response()->json(['message' => 'Funcionário não encontrado!', 'erro' => 's'], 200);
        }
    }

    public function listar_funcionarios(Request $request)
    {
        $funcionarios = Funcionario::all();

        return response()->json(['funcionarios' => $funcionarios], 200);
    }

    public function listar_funcionarios_simples(Request $request)
    {
        $funcionarios = Funcionario::select('nome', 'email')->get();

        return response()->json(['funcionarios' => $funcionarios], 200);
    }

    public function alterar_funcionario(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|exists:funcionario,id',
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|',
            'cargo' => 'required|string|',
        ]);

        try {

            $funcionario = Funcionario::find($request->id);

            if ($funcionario->email != $request->cpf) {
                $funcionario_email_igual = Funcionario::where('email', $request->email)->first();
                if ($funcionario_email_igual) {
                    return response()->json(['message' => 'Email já cadastrado!', 'erro' => 's'], 200);
                }
            }
            $funcionario->nome = $request->nome;
            $funcionario->email = $request->email;
            $funcionario->cargo = $request->cargo;
            $funcionario->save();

            return response()->json(['message' => 'Funcionário alterado com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao alterar funcionário!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function deletar_funcionario(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:funcionario,id',
        ]);

        try {
            $funcionario = Funcionario::find($request->id);
            $funcionario->delete();

            return response()->json(['message' => 'Funcionário deletado com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao deletar funcionário!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }
}
