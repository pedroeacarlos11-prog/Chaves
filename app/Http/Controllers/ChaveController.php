<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\ChaveResource;
use App\Http\Resources\Api\V1\SimpleItemResource;
use App\Models\Chave;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ChaveController extends Controller
{  
    public function salvar_chave(Request $request)
    {
        $request->validate([
            'nome_chave' => 'required|string|max:255',
            'localizacao' => 'required|string|email|max:255|unique:usuario',
            'status' => 'required|string|',
            
        ]);

        try {
            $chave = new Chave;
            $chave->nome_chave = $request->nome_chave;
            $chave->localizacao = $request->localizacao;
            $chave->status = $request->status;
            $chave->save();

            return response()->json(['message' => 'Chave cadastrada com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar chave!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function ver_chave(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $chave = Chave::find($request->id);
        if ($chave) {
            return response()->json(['chave' => $chave, 'erro' => 'n'], 200);
        } else {
            return response()->json(['message' => 'Chave não encontrada!', 'erro' => 's'], 200);
        }
    }

    public function listar_chaves(Request $request)
    {
        $chaves = Chave::all();

        return response()->json(['chaves' => $chaves], 200);
    }

    public function listar_chaves_simples(Request $request)
    {
        $chaves = Chave::select('id', 'nome_chave')->get();

        return response()->json(['chaves' => $chaves], 200);
    }

    public function alterar_chave(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|exists:chave,id',
            'nome_chave' => 'required|string|max:255',
            'localizacao' => 'required|string|email|max:255|unique:chave,localizacao,' . $request->id,
            'status' => 'required|string|',
        ]);

        try {

            $chave = Chave::find($request->id);

            if ($chave) {
                $chave->nome_chave = $request->nome_chave;
                $chave->localizacao = $request->localizacao;
                $chave->status = $request->status;

                $chave->save();

                return response()->json(['message' => 'Chave alterada com sucesso!', 'erro' => 'n'], 200);
            } else {
                return response()->json(['message' => 'Chave não encontrada!', 'erro' => 's'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao alterar chave!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function deletar_chave(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:chave,id',
        ]);

        try {
            $chave = Chave::find($request->id);
            $chave->delete();

            return response()->json(['message' => 'Chave deletada com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao deletar chave!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }
}
                }
            }
            $chave->nome_chave = $request->nome_chave;
            $chave->localizacao = $request->localizacao;
            $chave->status = $request->status;

            $chave->save();

            return response()->json(['message' => 'Chave alterada com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao alterar chave!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }

    public function deletar_chave(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:chave,id',
        ]);

        try {
            $chave = Chave::find($request->id);
            $chave->delete();

            return response()->json(['message' => 'Chave deletada com sucesso!', 'erro' => 'n'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao deletar chave!', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }
    }
}
