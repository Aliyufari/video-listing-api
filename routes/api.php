<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'index']);

Route::group(['prefix' => 'videos'], function () {
    Route::get('', [VideoController::class, 'index']);
    Route::post('', [VideoController::class, 'store']);
    Route::get('/{video}', [VideoController::class, 'show']);
    Route::put('/{video}', [VideoController::class, 'update']);
    Route::delete('/{video}', [VideoController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
