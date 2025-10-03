<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Agrega aquí la línea de importación de tu controlador
use App\Http\Controllers\ControleController;
use App\Http\Controllers\AdjuntoController;
Route::post('/autenticar', [ControleController::class, 'autenticar']);
Route::get('/controles', [ControleController::class, 'index']);
Route::get('/delegado/{ci_delegado}', [ControleController::class, 'listaMesasxDelegado']);
Route::put('/mesa/{id}', [ControleController::class, 'actualizarMesa']);
Route::post('/adjunto', [AdjuntoController::class, 'store']);
Route::get('/', function () {
    return view('welcome'); // O un simple return "¡Backend funcionando!";
});