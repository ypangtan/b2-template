<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    RoleService,
};

class RoleController extends Controller
{
    public function index() {
        
        $this->data['header']['title'] = __( 'template.roles' );
        $this->data['content'] = 'admin.role.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.roles' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.roles' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }
    
    public function add() {
        
        $this->data['header']['title'] = __( 'template.roles' );
        $this->data['content'] = 'admin.role.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.roles' ),
            'title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.roles' ) ) ] ),
            'mobile_title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.roles' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function edit() {
        
        $this->data['header']['title'] = __( 'template.roles' );
        $this->data['content'] = 'admin.role.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.roles' ),
            'title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.roles' ) ) ] ),
            'mobile_title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.roles' ) ) ] ),
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
}
