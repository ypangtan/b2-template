<?php

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\Admin\{
    AdministratorController,
    AuditController,
    DashboardController,
    ProfileController,
    SettingController,
};

Route::prefix( 'base2_admin' )->group( function() {

    Route::middleware( 'auth:admin' )->group( function() {

        Route::get( 'setup', [ SettingController::class, 'firstSetup' ] )->name( 'admin.first_setup' );
        Route::post( 'settings/setup_mfa', [ SettingController::class, 'setupMFA' ] );
        Route::post( 'settings/reset_mfa', [ SettingController::class, 'resetMFA' ] );

        Route::get( 'verify', [ AdministratorController::class, 'verify' ] )->name( 'admin.verify' );
        Route::post( 'verify-code', [ AdministratorController::class, 'verifyCode' ] )->name( 'admin.verifyCode' );

        Route::post( 'administrators/logout', [ AdministratorController::class, 'logoutLog' ] );
        Route::post( 'administrators/updateNotificationBox', [ AdministratorController::class, 'updateNotificationBox' ] );
        Route::post( 'administrators/updateNotificationSeen', [ AdministratorController::class, 'updateNotificationSeen' ] );
        
        Route::group( [ 'middleware' => [ 'checkAdminIsMFA', 'checkMFA' ] ], function() {
            
            Route::prefix( 'dashboard' )->group( function() {
                Route::get( '/', [ DashboardController::class, 'index' ] );
                Route::post( 'total_datas', [ DashboardController::class, 'totalDatas' ] );
                Route::post( 'monthly_sales', [ DashboardController::class, 'monthlySales' ] );
            } );

            Route::prefix( 'administrators' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add admins|view admins|edit admins|delete admins' ] ], function() {
                    Route::get( '/', [ AdministratorController::class, 'index' ] );
                    Route::get( 'roles', [ AdministratorController::class, 'role' ] );
                    Route::get( 'modules', [ AdministratorController::class, 'module' ] );
                } );

                Route::post( 'create_admin', [ AdministratorController::class, 'createAdmin' ] );
                Route::post( 'all_admins', [ AdministratorController::class, 'getAdmins' ] );
                Route::post( 'one_admin', [ AdministratorController::class, 'getAdmin' ] );
                Route::post( 'update_admin', [ AdministratorController::class, 'updateAdmin' ] );
        
                Route::post( 'create_module', [ AdministratorController::class, 'createModule' ] );
                Route::post( 'all_modules', [ AdministratorController::class, 'getModules' ] );
                Route::post( 'one_module', [ AdministratorController::class, 'getModule' ] );

                Route::post( 'create_role', [ AdministratorController::class, 'createRole' ] );
                Route::post( 'all_roles', [ AdministratorController::class, 'getRoles' ] );
                Route::post( 'one_role', [ AdministratorController::class, 'getRole' ] );
                Route::post( 'update_role', [ AdministratorController::class, 'updateRole' ] );
            } );

            Route::prefix( 'audit-logs' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add audits|view audits|edit audits|delete audits' ] ], function() {
                    Route::get( '/', [ AuditController::class, 'index' ] );
                } );

                Route::post( 'all_audits', [ AuditController::class, 'allAudits' ] );
                Route::post( 'one_audit', [ AuditController::class, 'oneAudit' ] );
            } );

            Route::prefix( 'settings' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add admins|view settings|edit settings|delete settings' ] ], function() {
                    Route::get( '/', [ SettingController::class, 'index' ] );
                } );
            } );

            Route::prefix( 'profile' )->group( function() {

                Route::get( '/', [ ProfileController::class, 'index' ] );

                Route::post( 'update', [ ProfileController::class, 'update' ] );
            } );

        } );

    } );

    Route::get( 'lang/{lang}', function( $lang ) {

        if( array_key_exists( $lang, Config::get( 'languages' ) ) ) {
            Session::put( 'appLocale', $lang );
        }
        
        return Redirect::back();
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

