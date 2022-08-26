<?php

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Http\Request;

use App\Http\Controllers\Branch\{
    DashboardController,
};

Route::prefix( 'base2_branch' )->group( function() {

    Route::middleware( [ 'auth:branch' ] )->group( function() {

        Route::prefix( 'dashboard' )->group( function() {
            Route::get( '/', [ DashboardController::class, 'index' ] );
            Route::post( 'monthly_sales', [ DashboardController::class, 'monthly_sales' ] );
        } );
    } );

    Route::get( '/login', function() {
        $data['content'] = 'branch.auth.login';
        return view( 'branch.main_pre_auth' )->with( $data );
    } )->middleware( 'guest:branch' )->name( 'branch.login' );

    $limiter = config( 'fortify.limiters.login' );

    Route::post( '/login', [AuthenticatedSessionController::class, 'store'] )->middleware( array_filter( ['guest:branch', $limiter ? 'throttle:'.$limiter : null,] ) );

    Route::post( '/logout', [AuthenticatedSessionController::class, 'destroy'] )->middleware( 'auth:branch' )->name( 'branch.logout' );
} );