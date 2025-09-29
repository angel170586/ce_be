<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    use HasFactory;

    // 1. Especificar el nombre de la tabla si no sigue la convención plural de Laravel (adjuntos).
    protected $table = 'adjunto'; 

    // 2. Definir la clave primaria si es diferente de 'id' o si quieres especificar el tipo.
    protected $primaryKey = 'id';
    
    // 3. Desactivar el uso de las columnas 'created_at' y 'updated_at' 
    //    si tu tabla no las tiene (tu tabla usa 'fecha_registro').
    public $timestamps = false;
    
    // 4. Permitir asignación masiva para las siguientes columnas
    protected $fillable = [
        'id_control',
        'nombre_archivo',
        'id_estado',
        'fecha_registro',
        'ci_delegado',
        'ruta',
    ];

    // Opcional: Si quieres que 'fecha_registro' sea un objeto Carbon al leerlo
    protected $casts = [
        'fecha_registro' => 'datetime',
    ];
}