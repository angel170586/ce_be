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

        // 2. guardar el registro sin el archivo por ahora, y obtener el id
        $adjunto = Adjunto::create([
            'id_control'     => $request->id_control,
            'nombre_archivo' => $regControl->numero_mesa . "_" . $contador, // Se actualizará después de subir el archivo
            'id_estado'      => 2, // Asume un estado inicial de 'activo' o 'pendiente'
            'fecha_registro' => now(),
            'ci_delegado'    => $request->ci_delegado,
            'ruta'           => $regControl->reci, // Se actualizará después de subir el archivo
        ]);

        return response()->json($adjunto->id, 201);


        // // 2. Procesar la subida del archivo
        // $file = $request->file('archivo');
        // $fileName = time() . '_' . $file->getClientOriginalName();
        
        // // La ruta donde se guarda el archivo (ej: storage/app/adjuntos/)
        // $path = $file->storeAs('adjuntos', $fileName); 

        // // 3. Crear el registro en la base de datos
        // $adjunto = Adjunto::create([
        //     'id_control'     => $request->id_control,
        //     'nombre_archivo' => $fileName,
        //     'id_estado'      => 1, // Asume un estado inicial de 'activo' o 'pendiente'
        //     'fecha_registro' => now(),
        //     'ci_delegado'    => $request->ci_delegado,
        //     'ruta'           => $path,
        // ]);

        // return response()->json($adjunto, 201);
    }

    // Muestra un adjunto específico
    public function show(string $id)
    {
        $adjunto = Adjunto::findOrFail($id);
        return response()->json($adjunto);
    }

    // // Elimina un adjunto (y su archivo asociado)
    // public function destroy(string $id)
    // {
    //     $adjunto = Adjunto::findOrFail($id);

    //     // 1. Eliminar el archivo del almacenamiento
    //     Storage::delete($adjunto->ruta);

    //     // 2. Eliminar el registro de la base de datos
    //     $adjunto->delete();

    //     return response()->json(null, 204);
    // }
}