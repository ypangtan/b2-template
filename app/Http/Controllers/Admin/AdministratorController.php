<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    AdministratorService,
};

use App\Models\{
    Role as RoleModel,
};

use Helper;

class AdministratorController extends Controller {

    public function index() {

        $this->data['header']['title'] = __( 'template.administrators' );
        $this->data['content'] = 'admin.administrator.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.administrators' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.administrators' ),
        ];

        $roles = [];
        foreach( \DB::table( 'roles' )->select( 'id', 'name' )->orderBy( 'id', 'ASC' )->get() as $role ) {
            array_push( $roles, [ 'key' => $role->name, 'value' => $role->id, 'title' => __( 'administrator.' . $role->name ) ] );
        }

        $this->data['data']['roles'] = $roles;

        return view( 'admin.main' )->with( $this->data );
    }

    public function add() {

        $this->data['header']['title'] = __( 'template.administrators' );
        $this->data['content'] = 'admin.administrator.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.administrators' ),
            'title' => __( 'template.add_x', [ 'title' => __( 'template.administrators' ) ] ),
            'mobile_title' => __( 'template.add_x', [ 'title' => __( 'template.administrators' ) ] ),
        ];
        $roles = [];
        foreach( RoleModel::orderBy( 'id', 'ASC' )->get() as $role ) {
            $roles[] = [ 'key' => $role->name, 'value' => $role->id, 'title' => __( 'role.' . $role->name ) ];
        }

        $this->data['data']['roles'] = $roles;

        return view( 'admin.main' )->with( $this->data );
    }

    public function edit() {

        $this->data['header']['title'] = __( 'template.administrators' );
        $this->data['content'] = 'admin.administrator.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.administrators' ),
            'title' => __( 'template.edit_x', [ 'title' => __( 'template.administrators' ) ] ),
            'mobile_title' => __( 'template.edit_x', [ 'title' => __( 'template.administrators' ) ] ),
        ];
        $roles = [];
        foreach( RoleModel::orderBy( 'id', 'ASC' )->get() as $role ) {
            $roles[] = [ 'key' => $role->name, 'value' => $role->id, 'title' => __( 'role.' . $role->name ) ];
        }

        $this->data['data']['roles'] = $roles;

        return view( 'admin.main' )->with( $this->data );
    }

    public function allAdministrators( Request $request ) {

        return AdministratorService::allAdministrators( $request );
    }

    public function oneAdministrator( Request $request ) {

        return AdministratorService::oneAdministrator( $request );
    }

    public function createAdministrator( Request $request ) {

        return AdministratorService::createAdministrator( $request );
    }

    public function updateAdministrator( Request $request ) {
        
        return AdministratorService::updateAdministrator( $request );
    }

    public function logoutLog( Request $request ) {
        
        return AdministratorService::logoutLog( $request );
    }

    public function updateNotificationSeen( Request $request ) {

        return AdministratorService::updateNotificationSeen( $request );
    }

    public function module() {

        $this->data['header']['title'] = __( 'template.modules' );
        $this->data['content'] = 'admin.administrator.module';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.administrators' ),
            'title' => __( 'template.modules' ),
            'mobile_title' => __( 'template.modules' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allModules( Request $request ) {

        return ModuleService::allModules( $request );
    }

    public function oneModule( Request $request ) {

        return ModuleService::oneModule($request  );
    }

    public function createModule( Request $request ) {

        return ModuleService::createModule( $request );
    }

    public function updateModule( Request $request ) {

        return ModuleService::updateModule($request  );
    }

    public function deleteModule( Request $request ) {

        return ModuleService::deleteModule( $request );
    }

    public function role() {
        
        $this->data['header']['title'] = __( 'template.roles' );
        $this->data['content'] = 'admin.administrator.role';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.administrators' ),
            'title' => __( 'template.roles' ),
            'mobile_title' => __( 'template.roles' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allRoles( Request $request ) {

        return RoleService::allRoles( $request );
    }

    public function oneRole( Request $request ) {

        return RoleService::oneRole( $request );
    }

    public function createRole( Request $request ) {

        return RoleService::createRole( $request );
    }

    public function updateRole( Request $request ) {
        
        return RoleService::updateRole( $request );
    }

    public function verify( Request $request ) {

        $value = $request->session()->get( 'mfa-ed' );

        if ( $value ) {
            return redirect()->route( 'admin.dashboard' );
        }
        
        $this->data['header']['title'] = __( 'template.verify_account' );
        
        $this->data['content'] = 'admin.administrator.verify';

        return view( 'admin.main_blank' )->with( $this->data );
    }

    public function verifyCode( Request $request ) {

        return AdministratorService::verifyCode( $request );
    }
}
