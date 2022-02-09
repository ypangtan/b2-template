<?php

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\Admin\{
    DashboardController
};

Route::prefix( 'base2_admin' )->group( function() {

    Route::middleware( 'auth:admin' )->group( function() {

        Route::prefix( 'dashboard' )->group( function() {
            Route::get( '/', [ DashboardController::class, 'index' ] );
            Route::post( 'monthly_sales', [ DashboardController::class, 'monthlySales' ] );
        } );

    } );

    Route::get( '/login', function() {

        $data['basic'] = true;
        $data['content'] = 'admin.auth.login';

        return view( 'admin.main_pre_auth' )->with( $data );

    } )->middleware( 'guest:admin' )->name( 'admin.login' );

    $limiter = config( 'fortify.limiters.login' );

    Route::post( '/login', [ AuthenticatedSessionController::class, 'store' ] )->middleware( array_filter( [ 'guest:admin', $limiter ? 'throttle:'.$limiter : null ] ) );

    Route::post( '/logout', [ AuthenticatedSessionController::class, 'destroy' ] )->middleware( 'auth:admin' )->name( 'admin.logout' );
} );

