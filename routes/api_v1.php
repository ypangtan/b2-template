<?php

use App\Http\Controllers\Api\V1\{
    CategoryController,
    NotificationController,
    ProductController,
    UserController,
};

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

/* Start Public route */
Route::post( 'otp', [ UserController::class, 'requestOtp' ] );
Route::post( 'otp/resend', [ UserController::class, 'resendOtp' ] );

Route::prefix( 'users' )->group( function() {

    Route::post( 'register', [ UserController::class, 'createUser' ] );

    // Login & create token
    Route::post( 'token', [ UserController::class, 'createToken' ] );
    // Social Login
    Route::post( 'social', [ UserController::class, 'createTokenSocial' ] );
} );
/* End Public route */

/* Start Protected route */
Route::middleware( 'auth:sanctum' )->group( function() {

    Route::prefix( 'users' )->group( function() {
        Route::get( '/', [ UserController::class, 'getUser' ] );
        Route::get( '/kyc-status', [ UserController::class, 'kycStatus' ] );
        Route::get( '/wallet-infos', [ UserController::class, 'walletInfos' ] );
        Route::get( '/all-users', [ UserController::class, 'allUsers' ] );
        Route::get( '/all-downlines', [ UserController::class, 'allDownlines' ] );

        Route::post( '/', [ UserController::class, 'updateUser' ] );
        Route::post( '/search-my-team', [ UserController::class, 'searchMyTeam' ] );
        Route::post( '/update-password', [ UserController::class, 'updateUserPassword' ] );
        Route::post( '/update-security-pin', [ UserController::class, 'updateSecurityPin' ] );
        Route::post( '/update-user-photo', [ UserController::class, 'updateUserPhoto' ] );
    } );

    Route::prefix( 'my-team' )->group( function() {
        Route::post( '/init-my-team', [ UserController::class, 'initMyTeam' ] );
        Route::post( '/my-team-ajax', [ UserController::class, 'myTeamAjax' ] );
    } );

    Route::prefix( 'notification' )->group( function() {
        Route::get( '/all-notification', [ NotificationController::class, 'allNotification' ] );
        Route::post( '/one-notification', [ NotificationController::class, 'oneNotification' ] );
        Route::post( '/all-read-notification', [ NotificationController::class, 'allReadNotification' ] );
    } );
} );