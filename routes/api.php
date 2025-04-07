<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('videos')->group(function () {
    Route::get('', [VideoController::class, 'index']);
    Route::post('', [VideoController::class, 'store']);
    Route::get('/{video}', [VideoController::class, 'show']);
    Route::put('/{video}', [VideoController::class, 'update']);
    Route::delete('/{video}', [VideoController::class, 'destroy']);
});
