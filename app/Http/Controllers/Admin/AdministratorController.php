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

        $this->data['content'] = 'admin.administrator.index';

        $roles = [];
        foreach( \DB::table( 'roles' )->select( 'id', 'name' )->orderBy( 'id', 'ASC' )->get() as $role ) {
            array_push( $roles, [ 'key' => $role->name, 'value' => $role->id, 'title' => __( 'administrator.' . $role->name ) ] );
        }

        $this->data['data']['roles'] = $roles;

        return view( 'admin.main' )->with( $this->data );
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

    public function module() {

        $this->data['content'] = 'admin.administrator.module';

        return view( 'admin.main' )->with( $this->data );
    }

    public function getModules( Request $request, ModuleService $moduleService ) {

        return response()->json( $moduleService->all( $request ) );
    }

    public function createModule( Request $request, ModuleService $moduleService ) {

        $moduleService->create( $request );
    }

    public function role() {
        
        $this->data['content'] = 'admin.administrator.role';

        return view( 'admin.main' )->with( $this->data );
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
}
