<?php

use App\Http\Controllers\Api\V1\{
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
        // Route::post( '/', [ UserController::class, 'updateUser' ] );
        // Route::post( 'password', [ UserController::class, 'updateUserPassword' ] );
        // Route::delete( '/', [ UserController::class, 'deleteUser' ] );
    } );
} );