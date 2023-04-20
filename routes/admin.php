<?php

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\Admin\{
    AdministratorController,
    AuditController,
    CategoryController,
    DashboardController,
    ModuleController,
    OrderController,
    ProductController,
    ProfileController,
    RoleController,
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

                Route::group( [ 'middleware' => [ 'permission:add administrators|view administrators|edit administrators|delete administrators' ] ], function() {
                    Route::get( '/', [ AdministratorController::class, 'index' ] )->name( 'admin.module_parent.administrator.index' );
                    Route::get( 'add', [ AdministratorController::class, 'add' ] )->name( 'admin.administrator.add' );
                    Route::get( 'edit', [ AdministratorController::class, 'edit' ] )->name( 'admin.administrator.edit' );
                } );

                Route::post( 'create-administrator', [ AdministratorController::class, 'createAdministrator' ] )->name( 'admin.administrator.createAdministrator' );
                Route::post( 'all-administrators', [ AdministratorController::class, 'allAdministrators' ] )->name( 'admin.administrator.allAdministrators' );
                Route::post( 'one-administrator', [ AdministratorController::class, 'oneAdministrator' ] )->name( 'admin.administrator.oneAdministrator' );
                Route::post( 'update-administrator', [ AdministratorController::class, 'updateAdministrator' ] )->name( 'admin.administrator.updateAdministrator' );
            } );

            Route::prefix( 'roles' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add roles|view roles|edit roles|delete roles' ] ], function() {
                    Route::get( '/', [ RoleController::class, 'index' ] )->name( 'admin.module_parent.role.index' );
                    Route::get( 'add', [ RoleController::class, 'add' ] )->name( 'admin.role.add' );
                    Route::get( 'edit', [ RoleController::class, 'edit' ] )->name( 'admin.role.edit' );
                } );

                Route::post( 'all-roles', [ RoleController::class, 'allRoles' ] )->name( 'admin.role.allRoles' );
                Route::post( 'one-role', [ RoleController::class, 'oneRole' ] )->name( 'admin.role.oneRole' );
                Route::post( 'create-role', [ RoleController::class, 'createRole' ] )->name( 'admin.role.createRole' );
                Route::post( 'update-role', [ RoleController::class, 'updateRole' ] )->name( 'admin.role.updateRole' );
            } );

            Route::prefix( 'modules' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add modules|view modules|edit modules|delete modules' ] ], function() {
                    Route::get( '/', [ ModuleController::class, 'index' ] )->name( 'admin.module_parent.module.index' );
                } );

                Route::post( 'create-module', [ ModuleController::class, 'createModule' ] )->name( 'admin.module.createModule' );
                Route::post( 'all-modules', [ ModuleController::class, 'allModules' ] )->name( 'admin.module.allModules' );
                Route::post( 'one-module', [ RoleModuleControllerController::class, 'oneModule' ] )->name( 'admin.module.oneModule' );
            } );

            Route::prefix( 'audit-logs' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add audits|view audits|edit audits|delete audits' ] ], function() {
                    Route::get( '/', [ AuditController::class, 'index' ] )->name( 'admin.module_parent.audit.index' );
                } );

                Route::post( 'all-audits', [ AuditController::class, 'allAudits' ] )->name( 'admin.audit.allAudits' );
                Route::post( 'one-audit', [ AuditController::class, 'oneAudit' ] )->name( 'admin.audit.oneAudit' );
            } );

            Route::prefix( 'categories' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add categories|view categories|edit categories|delete categories' ] ], function() {
                    Route::get( '/', [ CategoryController::class, 'index' ] )->name( 'admin.module_parent.category.index' );
                    Route::get( 'add', [ CategoryController::class, 'add' ] )->name( 'admin.category.add' );
                    Route::get( 'edit', [ CategoryController::class, 'edit' ] )->name( 'admin.category.edit' );
                } );

                Route::post( 'all-categories', [ CategoryController::class, 'allCategories' ] )->name( 'admin.category.allCategories' );
                Route::post( 'one-category', [ CategoryController::class, 'oneCategory' ] )->name( 'admin.category.oneCategory' );
                Route::post( 'create-category', [ CategoryController::class, 'createCategory' ] )->name( 'admin.category.createCategory' );
                Route::post( 'update-category', [ CategoryController::class, 'updateCategory' ] )->name( 'admin.category.updateCategory' );

                Route::post( 'structure', [ CategoryController::class, 'getCategoryStructure' ] )->name( 'admin.category.getCategoryStructure' );
            } );

            Route::prefix( 'products' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add products|view products|edit products|delete products' ] ], function() {
                    Route::get( '/', [ ProductController::class, 'index' ] )->name( 'admin.module_parent.product.index' );
                    Route::get( 'add', [ ProductController::class, 'add' ] )->name( 'admin.product.add' );
                    Route::get( 'edit', [ ProductController::class, 'edit' ] )->name( 'admin.product.edit' );
                } );

                Route::post( 'all-products', [ ProductController::class, 'allProducts' ] )->name( 'admin.product.allProducts' );
                Route::post( 'one-product', [ ProductController::class, 'oneProduct' ] )->name( 'admin.product.oneProduct' );
                Route::post( 'create-product', [ ProductController::class, 'createProduct' ] )->name( 'admin.product.createProduct' );
                Route::post( 'update-product', [ ProductController::class, 'updateProduct' ] )->name( 'admin.product.updateProduct' );

                Route::post( 'cke-upload', [ ProductController::class, 'ckeUpload' ] )->name( 'admin.product.ckeupload' );
            } );

            Route::prefix( 'orders' )->group( function() {
                Route::group( [ 'middleware' => [ 'permission:add orders|view orders|edit orders|delete orders' ] ], function() {
                    Route::get( '/', [ OrderController::class, 'index' ] )->name( 'admin.module_parent.order.index' );
                } );
            } );

            Route::prefix( 'settings' )->group( function() {

                Route::group( [ 'middleware' => [ 'permission:add settings|view settings|edit settings|delete settings' ] ], function() {
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

