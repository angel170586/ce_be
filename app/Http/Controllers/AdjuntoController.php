<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\Controle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para manejar archivos

class AdjuntoController extends Controller
{
    // Muestra una lista de todos los adjuntos
    public function index()
    {
        $adjuntos = Adjunto::all();
        return response()->json($adjuntos);
    }

    // Almacena un nuevo adjunto (incluye la lógica de subida de archivo)
    public function store(Request $request)
    {
        //registrar adjunto con los campos id_control, nombre_archivo, id_estado, fecha_registro, ci_delegado, ruta
        // // 1. Validación de los datos
        $request->validate([
            'id_control'     => 'required|integer',
            'archivo'        => 'required|file|max:10240', 
            'ci_delegado'    => 'required|string|max:500',
        ]);

        //validar que exista archivo
        if (!$request->hasFile('archivo')) {
            return response()->json(['error' => 'No se ha proporcionado ningún archivo'], 400);
        }
        $file = $request->file('archivo');

        $regControl= Controle::where('id', $request->id_control)->get(['id', 'numero_mesa','reci'])->first();
        if($regControl==null){
            return response()->json(['error' => 'Control no encontrado'], 404);
        }

        // buscar de base el id_control en la tabla adjunto
        $contador=1;
        $existe = Adjunto::where('id_control', $request->id_control)->get();
        if (count($existe) > 0) {
            $contador = count($existe) + 1;
        }

        $extension = $file->getClientOriginalExtension();
        $nombreArchivo = $regControl->numero_mesa . "_" . $contador . ".". $extension;
        // 2. guardar el registro sin el archivo por ahora, y obtener el id
        $adjunto = Adjunto::create([
            'id_control'     => $request->id_control,
            'nombre_archivo' => $nombreArchivo,
            'id_estado'      => 2, // Asume un estado inicial de 'activo' o 'pendiente'
            'fecha_registro' => now(),
            'ci_delegado'    => $request->ci_delegado,
            'ruta'           => $regControl->reci, // Se actualizará después de subir el archivo
        ]);

        //verificar que existe la carpeta storage/app/adjuntos, si no existe crearla
        if (!Storage::exists('adjuntos')) {
            Storage::makeDirectory('adjuntos');
        }
        //verificar que existe la carpeta storage/app/adjuntos/ + la variable ruta, si no existe crearla
        if (!Storage::exists('adjuntos/' . $regControl->reci)) {
            Storage::makeDirectory('adjuntos/' . $regControl->reci);
        }

        //armar la ruta completa del archivo storage/app/adjuntos/ + la variable ruta + la variable nombre_archivo + la extension del archivo
        $file = $request->file('archivo');
        $path = $file->storeAs('adjuntos/' . $regControl->reci, $nombreArchivo);

        // 3. Actualizar el registro con la ruta del archivo
        $adjunto->id_estado = 1;
        $adjunto->save();
        return response()->json($adjunto->id, 201);
    }

    // Muestra un adjunto específico
    public function show(string $id)
    {
        $adjunto = Adjunto::findOrFail($id);
        return response()->json($adjunto);
    }    
}