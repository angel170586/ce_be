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
        $delegado = Controle::where('ci_delegado', $ci_delegado)->get(['mesa','libre','pdc','validos','observacion','id','numero_mesa','reci','blancos','nulos','total']);

        if (!$delegado) {
            return response()->json(['message' => 'Delegado no encontrado'], 404);
        }

        return response()->json([
            'message' => 'Mesas encontradas',
            'mesas' => $delegado
        ]);
    }

 
    public function actualizarMesa(Request $request, $id)
    {
        // 1. Validación de los datos
        $this->validate($request, [
            'libre' => 'required|integer',
            'pdc' => 'required|integer',
            'validos' => 'required|integer',
            'blancos' => 'required|integer', //Nuevo campo
            'nulos' => 'required|integer',   //Nuevo campo
            'total' => 'required|integer',   //Nuevo campo
            'observacion' => 'nullable|string',
            'ci_modificacion' => 'nullable|string', // Opcional: quien modifica
        ]);

        // 2. Buscar el registro (Mesa)
        $mesa = Controle::find($id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        // 3. Actualizar los campos
        $mesa->libre = $request->input('libre');
        $mesa->pdc = $request->input('pdc');
        $mesa->validos = $request->input('validos');
        $mesa->blancos = $request->input('blancos');   // Actualización
        $mesa->nulos = $request->input('nulos');       // Actualización
        $mesa->total = $request->input('total');       // Actualización
        $mesa->observacion = $request->input('observacion');
        $mesa->ci_modificacion = $request->input('ci_modificacion'); // Opcional: actualizar quien modificó

        // 4. Guardar los cambios
        $mesa->save();

        // 5. Devolver respuesta
        return response()->json([
            'message' => 'Mesa actualizada correctamente',
            'mesa' => $mesa->id,
            'datos_enviados' => $mesa // Opcional: para confirmar los datos guardados
        ]);
    }
}