<?php

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\Admin\{
    AdministratorController,
    AuditController,
    DashboardController,
    ProfileController,
    SettingController,
};

Route::prefix( 'backoffice' )->group( function() {

    Route::middleware( 'auth:admin' )->group( function() {

        Route::get( 'setup', [ SettingController::class, 'firstSetup' ] )->name( 'admin.first_setup' );
        Route::post( 'settings/setup-mfa', [ SettingController::class, 'setupMFA' ] )->name( 'admin.setupMFA' );
        Route::post( 'settings/reset-mfa', [ SettingController::class, 'resetMFA' ] )->name( 'admin.resetMFA' );

        Route::get( 'verify', [ AdministratorController::class, 'verify' ] )->name( 'admin.verify' );
        Route::post( 'verify-code', [ AdministratorController::class, 'verifyCode' ] )->name( 'admin.verifyCode' );

        Route::prefix( 'administrators' )->group( function() {
            Route::post( 'logout', [ AdministratorController::class, 'logoutLog' ] )->name( 'admin.logoutLog' );
            Route::post( 'update-notification-seen', [ AdministratorController::class, 'updateNotificationSeen' ] )->name( 'admin.updateNotificationSeen' );
        } );
        
        Route::group( [ 'middleware' => [ 'checkAdminIsMFA', 'checkMFA' ] ], function() {
            
            Route::prefix( 'dashboard' )->group( function() {
                Route::get( '/', [ DashboardController::class, 'index' ] )->name( 'admin.dashboard.index' );
                Route::post( 'total_datas', [ DashboardController::class, 'totalDatas' ] );
                Route::post( 'monthly_sales', [ DashboardController::class, 'monthlySales' ] );
            } );

            Route::prefix( 'administrators' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add admins|view admins|edit admins|delete admins' ] ], function() {
                    Route::get( '/', [ AdministratorController::class, 'index' ] )->name( 'admin.administrator.index' );
                    Route::get( 'roles', [ AdministratorController::class, 'role' ] )->name( 'admin.administrator.role' );
                    Route::get( 'modules', [ AdministratorController::class, 'module' ] )->name( 'admin.administrator.module' );
                } );

                Route::post( 'create-admin', [ AdministratorController::class, 'createAdmin' ] )->name( 'admin.administrator.createAdmin' );
                Route::post( 'all-admins', [ AdministratorController::class, 'allAdmins' ] )->name( 'admin.administrator.allAdmins' );
                Route::post( 'one-admin', [ AdministratorController::class, 'oneAdmin' ] )->name( 'admin.administrator.oneAdmin' );
                Route::post( 'update-admin', [ AdministratorController::class, 'updateAdmin' ] )->name( 'admin.administrator.updateAdmin' );
        
                Route::post( 'create-module', [ AdministratorController::class, 'createModule' ] )->name( 'admin.administrator.createModule' );
                Route::post( 'all-modules', [ AdministratorController::class, 'allModules' ] )->name( 'admin.administrator.allModules' );

                Route::post( 'create-role', [ AdministratorController::class, 'createRole' ] )->name( 'admin.administrator.createRole' );
                Route::post( 'all-roles', [ AdministratorController::class, 'allRoles' ] )->name( 'admin.administrator.allRoles' );
                Route::post( 'one-role', [ AdministratorController::class, 'oneRole' ] )->name( 'admin.administrator.oneRole' );
                Route::post( 'update-role', [ AdministratorController::class, 'updateRole' ] )->name( 'admin.administrator.updateRole' );
            } );

            Route::prefix( 'audit-logs' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add audits|view audits|edit audits|delete audits' ] ], function() {
                    Route::get( '/', [ AuditController::class, 'index' ] )->name( 'admin.audit.index' );
                } );

                Route::post( 'all-audits', [ AuditController::class, 'allAudits' ] )->name( 'admin.audit.allAudits' );
                Route::post( 'one-audit', [ AuditController::class, 'oneAudit' ] )->name( 'admin.audit.oneAudit' );
            } );

            Route::prefix( 'settings' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add admins|view settings|edit settings|delete settings' ] ], function() {
                    Route::get( '/', [ SettingController::class, 'index' ] );
                } );
            } );

            Route::prefix( 'profile' )->group( function() {

                Route::get( '/', [ ProfileController::class, 'index' ] )->name( 'admin.profile.index' );

                Route::post( 'update', [ ProfileController::class, 'update' ] )->name( 'admin.profile.update' );
            } );

        } );

    } );

    Route::get( 'lang/{lang}', function( $lang ) {

        if( array_key_exists( $lang, Config::get( 'languages' ) ) ) {
            Session::put( 'appLocale', $lang );
        }
        
        return Redirect::back();
    } )->name( 'admin.lang' );

    Route::get( '/login', function() {

        $data['basic'] = true;
        $data['content'] = 'admin.auth.login';

        return view( 'admin.main_pre_auth' )->with( $data );

    } )->middleware( 'guest:admin' )->name( 'admin.login' );

    $limiter = config( 'fortify.limiters.login' );

    Route::post( '/login', [ AuthenticatedSessionController::class, 'store' ] )->middleware( array_filter( [ 'guest:admin', $limiter ? 'throttle:'.$limiter : null ] ) );

    Route::post( '/logout', [ AuthenticatedSessionController::class, 'destroy' ] )->middleware( 'auth:admin' )->name( 'admin.logout' );
} );

