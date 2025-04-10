<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\ObsController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PersonRestrictionController;
use App\Http\Controllers\PersonUserController;
use App\Http\Controllers\TypeAddressController;
use App\Http\Controllers\TypeContactController;
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

        Route::prefix('person')->group(function () {
            Route::get('', [PersonController::class, 'index']);
            Route::get('/{id}', [PersonController::class, 'show']);
            Route::post('', [PersonController::class, 'store']);
            Route::put('/{id}', [PersonController::class, 'update']);
            Route::delete('/{id}', [PersonController::class, 'destroy']);
        });

        Route::prefix('person-user')->group(function () {
            Route::get('', [PersonUserController::class, 'index']);
            Route::get('/{id}', [PersonUserController::class, 'show']);
            Route::post('', [PersonUserController::class, 'store']);
            Route::put('/{id}', [PersonUserController::class, 'update']);
            Route::delete('/{id}', [PersonUserController::class, 'destroy']);
        });

        Route::prefix('person-restriction')->group(function () {
            Route::get('', [PersonRestrictionController::class, 'index']);
            Route::get('/{id}', [PersonRestrictionController::class, 'show']);
            Route::post('', [PersonRestrictionController::class, 'store']);
            Route::put('/{id}', [PersonRestrictionController::class, 'update']);
            Route::delete('/{id}', [PersonRestrictionController::class, 'destroy']);
        });

        Route::prefix('person-leader')->group(function () {
            Route::get('', [App\Http\Controllers\PersonLeaderController::class, 'index']);
        });

        Route::prefix('type-address')->group(function () {
            Route::get('', [TypeAddressController::class, 'index']);
            Route::get('/{id}', [TypeAddressController::class, 'show']);
            Route::post('', [TypeAddressController::class, 'store']);
            Route::put('/{id}', [TypeAddressController::class, 'update']);
            Route::delete('/{id}', [TypeAddressController::class, 'destroy']);
        });

        Route::prefix('address')->group(function () {
            Route::get('', [AddressController::class, 'index']);
            Route::get('/{id}', [AddressController::class, 'show']);
            Route::post('', [AddressController::class, 'store']);
            Route::put('/{id}', [AddressController::class, 'update']);
            Route::delete('/{id}', [AddressController::class, 'destroy']);
        });

        Route::prefix('type-contact')->group(function () {
            Route::get('', [TypeContactController::class, 'index']);
            Route::get('/{id}', [TypeContactController::class, 'show']);
            Route::post('', [TypeContactController::class, 'store']);
            Route::put('/{id}', [TypeContactController::class, 'update']);
            Route::delete('/{id}', [TypeContactController::class, 'destroy']);
        });

        Route::prefix('contact')->group(function () {
            Route::get('', [ContactController::class, 'index']);
            Route::get('/{id}', [ContactController::class, 'show']);
            Route::post('', [ContactController::class, 'store']);
            Route::put('/{id}', [ContactController::class, 'update']);
            Route::delete('/{id}', [ContactController::class, 'destroy']);
        });

        

    });
});
