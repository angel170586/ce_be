<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use Illuminate\Http\Request;

class ControleController extends Controller
{
public function index()
{
    $controles = Controle::take(10)->get();
    return response()->json($controles);
}

    public function autenticar(Request $request)
    {
        $this->validate($request, [
            'ci_delegado' => 'required',
            'celular_delegado' => 'required',
        ]);

        $delegado = Controle::where('ci_delegado', $request->input('ci_delegado'))
                           ->where('celular_delegado', $request->input('celular_delegado'))
                           ->first();

        if (!$delegado) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        return response()->json([
            'message' => 'Autenticación exitosa',
            'delegado' => $delegado
        ]);
    }
}