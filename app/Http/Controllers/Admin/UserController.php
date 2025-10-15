<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Services\{
    UserService,
};

class UserController extends Controller
{
    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.users' );
        $this->data['content'] = 'admin.template.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.users' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.users' ),
        ];

        $this->data['data']['model'] = User::class;
        $this->data['data']['status'] = [
            10 => [
                'color' => 'badge rounded-pill bg-success',
                'value' => '10',
                'title' => __( 'datatables.activated' ),
            ],
            20 => [
                'color' => 'badge rounded-pill bg-danger',
                'value' => '20',
                'title' => __( 'datatables.suspended' ),
            ],
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

    public function updateUserStatus( Request $request ) {

        return UserService::updateUserStatus( $request );
    }
}
