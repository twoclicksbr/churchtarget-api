<?php

use App\Http\Controllers\CredentialController;
use App\Http\Controllers\TypeGenderController;
use App\Http\Controllers\TypeUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/v1/setup/credential', [CredentialController::class, 'store']);

Route::prefix('v1')->middleware('verify.headers')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Rota pública funcionando']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/test', function () {
            return response()->json(['message' => 'Rota admin com headers funcionando']);
        });

        Route::prefix('credential')->group(function () {
            Route::get('', [CredentialController::class, 'index']);
            Route::get('/{id}', [CredentialController::class, 'show']);
            Route::post('', [CredentialController::class, 'store']);
            Route::put('/{id}', [CredentialController::class, 'update']);
            Route::delete('/{id}', [CredentialController::class, 'destroy']);
        });

        Route::prefix('type-gender')->group(function () {
            Route::get('', [TypeGenderController::class, 'index']);
            Route::get('/{id}', [TypeGenderController::class, 'show']);
            Route::post('', [TypeGenderController::class, 'store']);
            Route::put('/{id}', [TypeGenderController::class, 'update']);
            Route::delete('/{id}', [TypeGenderController::class, 'destroy']);
        });

        Route::prefix('type-user')->group(function () {
            Route::get('', [TypeUserController::class, 'index']);
            Route::get('/{id}', [TypeUserController::class, 'show']);
            Route::post('', [TypeUserController::class, 'store']);
            Route::put('/{id}', [TypeUserController::class, 'update']);
            Route::delete('/{id}', [TypeUserController::class, 'destroy']);
        });

    });
});
