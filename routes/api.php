<?php

use App\Http\Middleware\DisableCors;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::middleware(DisableCors::class)->group(function() {
    Route::post('session/generate', [\App\Http\Controllers\SessionController::class, 'generate']);

    Route::post('share', [\App\Http\Controllers\ShareController::class, 'save']);
    Route::get('share/{uuid}', [\App\Http\Controllers\ShareController::class, 'get']);
});
