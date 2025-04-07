<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->middleware('verify.headers')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Rota pública funcionando']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/test', function () {
            return response()->json(['message' => 'Rota admin com headers funcionando']);
        });
    });
});
