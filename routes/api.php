<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CompagnyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\LocalisationController;
use App\Http\Controllers\BordereauController;
use App\Http\Controllers\FamilleController;
use App\Http\Controllers\SousFamilleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CodeInventaireController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImmobilisationController;
use App\Http\Controllers\TransfertController;
use App\Http\Controllers\AvantRebusController;
use App\Http\Controllers\AuRebusController;
use App\Http\Controllers\NatureController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\BonCommandeController;
use App\Http\Controllers\AcquisitionController;

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

Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here

    //Route::group(['middleware' => ['role:admin|root']], function () {

        Route::apiResource('users', ApiAuthController::class)->only(['index', 'show', 'destroy', 'update']);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::post('/user/{user_id}/roles', [ApiAuthController::class, 'giveRolesToUser'])->name('giveRolesToUser');
        Route::delete('/user/{user_id}/roles', [ApiAuthController::class, 'removeRolesToUser'])->name('removeRolesToUser');

        Route::post('/role/{role_id}/permissions', [RoleController::class, 'givePermissionsToRole'])->name('givePermissionsToRole');
        Route::delete('/role/{role_id}/permissions', [RoleController::class, 'removePermissionsToRole'])->name('removePermissionsToRole');
    //});

    
});

Route::group(['middleware' => ['cors', 'json.response']], function () {

    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
    Route::get('/my-profile', [ApiAuthController::class, 'myProfile'])->name('myProfile');
    Route::post('/reset-password', [ApiAuthController::class, 'resetPassword'])->name('resetPassword');
    
    //after theses routes must be authorized
    Route::apiResource('compagnies', CompagnyController::class);
    Route::apiResource('zones', ZoneController::class);
    Route::apiResource('sites', SiteController::class);
    Route::apiResource('localisations', LocalisationController::class);
    Route::apiResource('bordereaux', BordereauController::class);
    Route::apiResource('familles', FamilleController::class);
    Route::apiResource('sous-familles', SousFamilleController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('code-inventaires', CodeInventaireController::class);
    Route::apiResource('immobilisations', ImmobilisationController::class);
    Route::apiResource('avant-rebus', AvantRebusController::class);
    Route::apiResource('au-rebus', AuRebusController::class);
    Route::apiResource('transferts', TransfertController::class);
    Route::apiResource('natures', NatureController::class);
    Route::apiResource('fournisseurs', FournisseurController::class);
    Route::apiResource('bon-commandes', BonCommandeController::class);
    Route::apiResource('acquisitions', AcquisitionController::class);


    Route::post('/acquisitions/verify-code-inventaire/{status?}', [AcquisitionController::class, 'verifyCodeInventaire'])->name('acquisitions.verify-code-inventaire');
    Route::post('/immobilisations/verify-localisation/{status?}', [ImmobilisationController::class, 'verifyLocalisation'])->name('immobilisations.verify_localisation');
    Route::get('/site/{zone_code}', [SiteController::class, 'getCode'])->name('getSiteCode');
    Route::get('/localisation/{site_code}', [LocalisationController::class, 'getCode'])->name('getLocalisationCode');
    Route::get('/bordereau/{localisation_code}/{year}', [BordereauController::class, 'getCode'])->name('getBordereauCode');
    Route::get('/bordereau/{code}', [BordereauController::class, 'create25InventaireCode'])->name('create25InventaireCode');
    //end after
    
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register');
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login');
    Route::post('/register-admin', [ApiAuthController::class, 'registerAdmin'])->name('registerAdmin');
    Route::post('/register-root', [ApiAuthController::class, 'registerRoot'])->name('registerRoot');
    Route::post('/upload', [ApiAuthController::class, 'upload'])->name('upload_api');
});
