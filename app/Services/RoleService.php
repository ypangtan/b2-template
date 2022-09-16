<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Models\Role;

use App\Models\{
    Role as RoleModel
};

class RoleService {

    public static function all( $request ) {
        $filter = false;

        $limit = $request->input( 'length' );
        $offset = $request->input( 'start' );

        $role = RoleModel::select( 'roles.*' );

        if( !empty( $search_date = $request->input( 'columns.1.search.value' ) ) ) {
            if( str_contains( $search_date, 'to' ) ) {
                $dates = explode( ' to ', $search_date );
                $role->whereBetween( 'roles.created_at', [ $dates[0] . ' 00:00:00' , $dates[1] . ' 23:59:59' ] );
            } else {
                $role->whereBetween( 'roles.created_at', [ $search_date . ' 00:00:00' , $search_date . ' 23:59:59' ] );
            }
            $filter = true;
        }
        
        if( !empty( $name = $request->input( 'columns.2.search.value' ) ) ) {
            $role->where( 'name', $name );
            $filter = true;
        }

        if( $request->input( 'order.0.column' ) != 0 ) {

            switch( $request->input( 'order.0.column' ) ) {
                case 1:
                    $role->orderBy( 'created_at', $request->input( 'order.0.dir' ) );
                    break;
                case 2:
                    $role->orderBy( 'name', $request->input( 'order.0.dir' ) );
                    break;
            }
        }

        $count_role = $role->count();
        
        $roles = $role->skip( $offset )->take( $limit )->get();

        $total = RoleModel::count();

        $data = array(
            'roles' => $roles,
            'draw' => $request->input( 'draw' ),
            'recordsFiltered' => $filter ? $count_role : $total,
            'recordsTotal' => $total,
        );

        return $data;
    }

    public static function one( $request ) {

        $permission = \DB::table( 'role_has_permissions' )->where( 'role_id', $request->id )->leftJoin( 'permissions', 'role_has_permissions.permission_id', '=', 'permissions.id' )->get();

        return [ 'role' => RoleModel::find( $request->input( 'id' ) ), 'permissions' => $permission ];
    }

    public static function create( $request ) {

        $request->validate( [
            'role_name' => 'required|unique:roles,name',
            'guard_name' => 'required',
        ] );

        $role = Role::create( [ 'name' => $request->role_name, 'guard_name' => $request->guard_name ] );

        if( !empty( $request->modules ) ) {
            foreach( $request->modules as $key => $module ) {

                $key = explode( '|', $key );
                if( $key[1] != $role_model->guard_name ) {
                    echo $key[1];
                    continue;
                }

                foreach( $module as $action ) {
                    $role->givePermissionTo( $action . ' ' . $key[0] );
                }
            }
        }
    }

    public static function update( $request ) {

        $role_model = RoleModel::find( $request->input( 'id' ) );
        $role = Role::findByName( $role_model->name, $role_model->guard_name );

        $permissions = [];
        
        if( $request->modules ) {
            foreach( $request->modules as $key => $module ) {
                var_dump($key);
                $key = explode( '|', $key );
                if( $key[1] != $role_model->guard_name ) {
                    echo $key[1];
                    continue;
                }
                foreach( $module as $action ) {
                    array_push( $permissions, $action . ' ' . $key[0] );
                }
            }
        }

        $role->syncPermissions( $permissions );
    }
}