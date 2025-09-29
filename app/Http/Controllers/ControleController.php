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

    public function listaMesasxDelegado(Request $request, $ci_delegado)
    {
        // mostrar las columnas mesa y ci_delegado
        // $delegado = Controle::where('ci_delegado', $ci_delegado)->get(['ci_delegado', 'mesa']);
        $delegado = Controle::where('ci_delegado', $ci_delegado)->get(['mesa','libre','pdc','validos','observacion','id']);

        if (!$delegado) {
            return response()->json(['message' => 'Delegado no encontrado'], 404);
        }

        return response()->json([
            'message' => 'Mesas encontradas',
            'mesas' => $delegado
        ]);
    }

    //funcion para actualizar los valores de libre, pdc, validos y observacion por id
    public function actualizarMesa(Request $request, $id)
    {
        $this->validate($request, [
            'libre' => 'required|integer',
            'pdc' => 'required|integer',
            'validos' => 'required|integer',
            'observacion' => 'nullable|string',
        ]);
        $mesa = Controle::find($id);
        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }
        $mesa->libre = $request->input('libre');
        $mesa->pdc = $request->input('pdc');
        $mesa->validos = $request->input('validos');
        $mesa->observacion = $request->input('observacion');
        $mesa->save();
        return response()->json([
            'message' => 'Mesa actualizada',
            'mesa' => $mesa->id
        ]);
    }
}