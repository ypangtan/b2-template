<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    Admin,
    Role as RoleModel
};

class AdminService {
    
    public function all( $request ) {

        $filter = false;

        $limit = $request->input( 'length' );
        $offset = $request->input( 'start' );

        $admin = Admin::select( 'admins.*', 'roles.name as role_name' );
        $admin->leftJoin( 'roles', 'admins.role', '=', 'roles.id' );

        if( !empty( $search_date = $request->input( 'columns.1.search.value' ) ) ) {
            if( str_contains( $search_date, 'to' ) ) {
                $dates = explode( ' to ', $search_date );
                $admin->whereBetween( 'admins.created_at', [ $dates[0] . ' 00:00:00' , $dates[1] . ' 23:59:59' ] );
            } else {
                $admin->whereBetween( 'admins.created_at', [ $search_date . ' 00:00:00' , $search_date . ' 23:59:59' ] );
            }
            $filter = true;
        }
        
        if( !empty( $username = $request->input( 'columns.2.search.value' ) ) ) {
            $admin->where( 'username', 'LIKE', "%{$username}%" );
            $filter = true;
        }

        if( !empty( $email = $request->input( 'columns.3.search.value' ) ) ) {
            $admin->where( 'email', 'LIKE', "%{$email}%" );
            $filter = true;
        }

        if( !empty( $role = $request->input( 'columns.4.search.value' ) ) ) {
            $admin->where( 'roles.id', $role );
            $filter = true;
        }

        if( $request->input( 'order.0.column' ) != 0 ) {

            switch( $request->input( 'order.0.column' ) ) {
                case 1:
                    $admin->orderBy( 'created_at', $request->input( 'order.0.dir' ) );
                    break;
                case 2:
                    $admin->orderBy( 'username', $request->input( 'order.0.dir' ) );
                    break;
                case 3:
                    $admin->orderBy( 'email', $request->input( 'order.0.dir' ) );
                    break;
            }
        }

        $count_admin = $admin->count();
        
        $admins = $admin->skip( $offset )->take( $limit )->get();

        $total = Admin::count();

        $data = array(
            'admins' => $admins,
            'draw' => $request->input( 'draw' ),
            'recordsFiltered' => $filter ? $count_admin : $total,
            'recordsTotal' => $total,
        );

        return $data;
    }

    public function one( $request ) {

        return Admin::find( $request->id );
    }

    public function create( $request ) {

        $request->validate( [
            'username' => 'required|max:25|unique:admins,username',
            'email' => 'required|max:25|unique:admins,email',
            'role' => 'required',
            'password' => 'required|min:8|max:255',
        ] );

        $admin = Admin::create( [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => \Hash::make( $request->password ),
        ] );

        $role_model = RoleModel::find( $request->role );

        $admin->syncRoles( [ $role_model->name ] );

        return $admin;
    }

    public function update( $request ) {

        $request->validate( [
            'username' => 'required|max:25|unique:admins,username,' . $request->id,
            'email' => 'required|max:25|unique:admins,email,' . $request->id,
            'role' => 'required',
            'password' => 'min:8|max:25',
        ] );

        $admin = Admin::find( $request->id );
        $admin->id = $request->id;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->role = $request->role;

        if( !empty( $request->password ) ) {
            $admin->password = \Hash::make( $request->password );
        }

        $role_model = RoleModel::find( $request->role );
        $admin->syncRoles( [ $role_model->name ] );
        $admin->save();

        return $admin;
    }
}