<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use Illuminate\Http\Request;

class EmprestimoController extends Controller
{
    public function salvar_emprestimo(Request $request)
    {
        $request->validate([
            'funcionario_id' => 'required|string|max:255',
            'chave_id' => 'required|string||max:255|',
            'data_retirada' => 'required|string|',
            'hora_retirada' => 'required|string|',
            'data_devolucao' => 'required|date',
            'hora_devolucao' => 'required|string|',
            'status' => 'required|string|',
        ]);

        try {
            $emprestimo = new Emprestimo;
            $emprestimo->funcionario_id = $request->funcionario_id;
            $emprestimo->chave_id = $request->chave_id;
            $emprestimo->data_retirada = $request->data_retirada;
            $emprestimo->hora_retirada = $request->hora_retirada;
            $emprestimo->data_devolucao = $request->data_devolucao;
            $emprestimo->hora_devolucao = $request->hora_devolucao;
            $emprestimo->status = $request->status;

            $emprestimo->save();

            return response()->json(['message' => 'Usuário cadastrado com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar usuário!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function ver_emprestimo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $emprestimo = Emprestimo::find($request->id);
        if ($emprestimo) {
            return response()->json(['emprestimo' => $emprestimo, 'erro' => 'n'], 200);
        } else {
            return response()->json(['message' => 'Empréstimo não encontrado!', 'erro' => 's'], 200);
        }
    }

    public function listar_emprestimos(Request $request)
    {
        $emprestimos = Emprestimo::all();

        return response()->json(['emprestimos' => $emprestimos], 200);
    }

    public function listar_emprestimos_simples(Request $request)
    {
        $emprestimos = Emprestimo::select('funcionario_id', 'chave_id')->get();

        return response()->json(['emprestimos' => $emprestimos], 200);
    }

    public function alterar_emprestimo(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|exists:emprestimo,id',
            'funcionario_id' => 'required|string|max:255',
            'chave_id' => 'required|string|max:255',
            'data_retirada' => 'required|string|',
            'hora_retirada' => 'required|string|',
            'data_devolucao' => 'required|date',
            'hora_devolucao' => 'required|string|',
            'status' => 'required|string|',
        ]);

        try {

            $emprestimo = Emprestimo::find($request->id);

            if ($emprestimo) {
                $emprestimo->funcionario_id = $request->funcionario_id;
                $emprestimo->chave_id = $request->chave_id;
                $emprestimo->data_retirada = $request->data_retirada;
                $emprestimo->hora_retirada = $request->hora_retirada;
                $emprestimo->data_devolucao = $request->data_devolucao;
                $emprestimo->hora_devolucao = $request->hora_devolucao;
                $emprestimo->status = $request->status;

                $emprestimo->save();

                return response()->json(['message' => 'Empréstimo alterado com sucesso!', 'erro' => 'n'], 200);
            } else {
                return response()->json(['message' => 'Empréstimo não encontrado!', 'erro' => 's'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao alterar empréstimo!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function deletar_emprestimo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:emprestimo,id',
        ]);

        try {
            $emprestimo = Emprestimo::find($request->id);
            $emprestimo->delete();

            return response()->json(['message' => 'Empréstimo deletado com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao deletar empréstimo!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }
}
