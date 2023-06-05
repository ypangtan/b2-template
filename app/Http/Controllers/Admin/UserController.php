<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    UserService,
};

class UserController extends Controller
{
    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.users' );
        $this->data['content'] = 'admin.user.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.users' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.users' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function add( Request $request ) {

        $this->data['header']['title'] = __( 'template.users' );
        $this->data['content'] = 'admin.user.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.users' ),
            'title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.users' ) ) ] ),
            'mobile_title' => __( 'template.add_x', [ 'title' => \Str::singular( __( 'template.users' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function edit( Request $request ) {

        $this->data['header']['title'] = __( 'template.users' );
        $this->data['content'] = 'admin.user.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.users' ),
            'title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.users' ) ) ] ),
            'mobile_title' => __( 'template.edit_x', [ 'title' => \Str::singular( __( 'template.users' ) ) ] ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allUsers( Request $request ) {

        return UserService::allUsers( $request );
    }

    public function oneUser( Request $request ) {

        return UserService::oneUser( $request );
    }

    public function createUser( Request $request ) {

        return UserService::createUserAdmin( $request );
    }

    public function updateUser( Request $request ) {

        return UserService::updateUserAdmin( $request );
    }
}
