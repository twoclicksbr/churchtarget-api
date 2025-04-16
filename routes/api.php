<?php

use App\Http\Controllers\Api\{
    AddressController,
    AuthController,
    ContactController,
    CredentialController,
    DocumentController,
    EmailConfigController,
    MediaLibraryController,
    MinistryController,
    MinistryCycleController,
    MinistryPersonRegisteredController,
    ObsController,
    PersonController,
    PersonLeaderController,
    PersonRestrictionController,
    PersonUserController,
    ShareController,
    TypeAddressController,
    TypeContactController,
    TypeDocumentController,
    TypeEmailConfigController,
    TypeEmailController,
    TypeGenderController,
    TypeGroupController,
    TypeParticipationController,
    TypeShareController,
    TypeUserController
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('verify.headers')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Rota pública funcionando']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::prefix('email')->group(function () {
            Route::post('/env', [AuthController::class, 'recPassword']);
            Route::post('/verify', [AuthController::class, 'verifyRecoveryCode']);
            Route::post('/reset', [AuthController::class, 'resetPassword']);

            Route::prefix('config')->group(function () {
                Route::get('', [EmailConfigController::class, 'index']);
                Route::get('/{id}', [EmailConfigController::class, 'show']);
                Route::post('', [EmailConfigController::class, 'store']);
                Route::put('/{id}', [EmailConfigController::class, 'update']);
                Route::delete('/{id}', [EmailConfigController::class, 'destroy']);
            });
        });
    });

    Route::prefix('email')->group(function () {
        Route::post('', [ShareController::class, 'store']);
    });

    Route::prefix('admin')->group(function () {
        
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
            Route::get('', [PersonLeaderController::class, 'index']);
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

        Route::prefix('type-share')->group(function () {
            Route::get('', [TypeShareController::class, 'index']);
            Route::get('/{id}', [TypeShareController::class, 'show']);
            Route::post('', [TypeShareController::class, 'store']);
            Route::put('/{id}', [TypeShareController::class, 'update']);
            Route::delete('/{id}', [TypeShareController::class, 'destroy']);
        });

        Route::prefix('share')->group(function () {
            Route::get('', [ShareController::class, 'index']);
            Route::get('/{id}', [ShareController::class, 'show']);
            Route::post('', [ShareController::class, 'store']);
            Route::put('/{id}', [ShareController::class, 'update']);
            Route::delete('/{id}', [ShareController::class, 'destroy']);
        });

        Route::prefix('type-email-config')->group(function () {
            Route::get('', [TypeEmailConfigController::class, 'index']);
            Route::get('/{id}', [TypeEmailConfigController::class, 'show']);
            Route::post('', [TypeEmailConfigController::class, 'store']);
            Route::put('/{id}', [TypeEmailConfigController::class, 'update']);
            Route::delete('/{id}', [TypeEmailConfigController::class, 'destroy']);
        });

        Route::prefix('type-email')->group(function () {
            Route::get('', [TypeEmailController::class, 'index']);
            Route::get('/{id}', [TypeEmailController::class, 'show']);
            Route::post('', [TypeEmailController::class, 'store']);
            Route::put('/{id}', [TypeEmailController::class, 'update']);
            Route::delete('/{id}', [TypeEmailController::class, 'destroy']);
        });

        Route::prefix('type-document')->group(function () {
            Route::get('', [TypeDocumentController::class, 'index']);
            Route::get('/{id}', [TypeDocumentController::class, 'show']);
            Route::post('', [TypeDocumentController::class, 'store']);
            Route::put('/{id}', [TypeDocumentController::class, 'update']);
            Route::delete('/{id}', [TypeDocumentController::class, 'destroy']);
        });

        Route::prefix('document')->group(function () {
            Route::get('', [DocumentController::class, 'index']);
            Route::get('/{id}', [DocumentController::class, 'show']);
            Route::post('', [DocumentController::class, 'store']);
            Route::put('/{id}', [DocumentController::class, 'update']);
            Route::delete('/{id}', [DocumentController::class, 'destroy']);
        });

        Route::prefix('media-library')->group(function () {
            Route::get('', [MediaLibraryController::class, 'index']);
            Route::get('/{id}', [MediaLibraryController::class, 'show']);
            Route::post('', [MediaLibraryController::class, 'store']);
            Route::delete('/{id}', [MediaLibraryController::class, 'destroy']);
        });

        Route::prefix('ministry')->group(function () {
            Route::get('', [MinistryController::class, 'index']);
            Route::get('/{id}', [MinistryController::class, 'show']);
            Route::post('', [MinistryController::class, 'store']);
            Route::put('/{id}', [MinistryController::class, 'update']);
            Route::delete('/{id}', [MinistryController::class, 'destroy']);
        });

        Route::prefix('ministry-cycle')->group(function () {
            Route::get('', [MinistryCycleController::class, 'index']);
            Route::get('/{id}', [MinistryCycleController::class, 'show']);
            Route::post('', [MinistryCycleController::class, 'store']);
            Route::put('/{id}', [MinistryCycleController::class, 'update']);
            Route::delete('/{id}', [MinistryCycleController::class, 'destroy']);
        });
        
        Route::prefix('ministry-person-registered')->group(function () {
            Route::get('', [MinistryPersonRegisteredController::class, 'index']);
            Route::get('/{id}', [MinistryPersonRegisteredController::class, 'show']);
            Route::post('', [MinistryPersonRegisteredController::class, 'store']);
            Route::put('/{id}', [MinistryPersonRegisteredController::class, 'update']);
            Route::delete('/{id}', [MinistryPersonRegisteredController::class, 'destroy']);
        });

    });
});



