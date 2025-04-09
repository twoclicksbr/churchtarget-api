<?php

use App\Http\Controllers\CredentialController;
use App\Http\Controllers\ObsController;
use App\Http\Controllers\TypeGenderController;
use App\Http\Controllers\TypeGroupController;
use App\Http\Controllers\TypeParticipationController;
use App\Http\Controllers\TypeShareController;
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

        Route::prefix('type-participation')->group(function () {
            Route::get('', [TypeParticipationController::class, 'index']);
            Route::get('/{id}', [TypeParticipationController::class, 'show']);
            Route::post('', [TypeParticipationController::class, 'store']);
            Route::put('/{id}', [TypeParticipationController::class, 'update']);
            Route::delete('/{id}', [TypeParticipationController::class, 'destroy']);
        });

        Route::prefix('type-share')->group(function () {
            Route::get('', [TypeShareController::class, 'index']);
            Route::get('/{id}', [TypeShareController::class, 'show']);
            Route::post('', [TypeShareController::class, 'store']);
            Route::put('/{id}', [TypeShareController::class, 'update']);
            Route::delete('/{id}', [TypeShareController::class, 'destroy']);
        });

        Route::prefix('type-group')->group(function () {
            Route::get('', [TypeGroupController::class, 'index']);
            Route::get('/{id}', [TypeGroupController::class, 'show']);
            Route::post('', [TypeGroupController::class, 'store']);
            Route::put('/{id}', [TypeGroupController::class, 'update']);
            Route::delete('/{id}', [TypeGroupController::class, 'destroy']);
        });

        Route::prefix('obs')->group(function () {
            Route::get('', [ObsController::class, 'index']);
            Route::get('/{id}', [ObsController::class, 'show']);
            Route::post('', [ObsController::class, 'store']);
            Route::put('/{id}', [ObsController::class, 'update']);
            Route::delete('/{id}', [ObsController::class, 'destroy']);
        });

    });
});
