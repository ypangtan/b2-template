<?php

use App\Http\Controllers\Api\V1\{
    CategoryController,
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

Route::prefix( 'products' )->group( function() {
    Route::get( '/', [ ProductController::class, 'getProducts' ] );
    Route::get( 'detail', [ ProductController::class, 'getProduct' ] );
} );
Route::prefix( 'categories' )->group( function() {
    Route::get( '/', [ CategoryController::class, 'getCategories' ] );
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