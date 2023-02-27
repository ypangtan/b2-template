<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    AdminService,
    ModuleService,
    RoleService
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

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function getAdmins( Request $request, AdminService $adminService ) {

        return response()->json( $adminService->all( $request ) );
    }

    public function getAdmin( Request $request, AdminService $adminService ) {

        return response()->json( $adminService->one( $request ) );
    }

    public function createAdmin( Request $request, AdminService $adminService ) {

        return response()->json( $adminService->create( $request ) );
    }

    public function updateAdmin( Request $request, AdminService $adminService ) {
        
        return response()->json( $adminService->update( $request ) );
    }

    public function logoutLog( Request $request ) {
        
        return AdminService::logoutLog( $request );
    }

    public function updateNotificationBox( Request $request ) {

        return AdminService::updateNotificationBox( $request );
    }

    public function updateNotificationSeen( Request $request ) {

        return AdminService::updateNotificationSeen( $request );
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

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function getModules( Request $request, ModuleService $moduleService ) {

        return response()->json( $moduleService->all( $request ) );
    }

    public function createModule( Request $request, ModuleService $moduleService ) {

        $moduleService->create( $request );
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

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function getRoles( Request $request, RoleService $roleService ) {

        return response()->json( $roleService->all( $request ) );
    }

    public function getRole( Request $request, RoleService $roleService ) {

        return response()->json( $roleService->one( $request ) );
    }

    public function createRole( Request $request, RoleService $roleService ) {

        $roleService->create( $request );
    }

    public function updateRole( Request $request, RoleService $roleService ) {
        
        $roleService->update( $request );
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

        return AdminService::verifyCode( $request );
    }
}
