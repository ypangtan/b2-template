<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Models\Role;

use App\Models\{
    Role as RoleModel
};

class RoleService {

    public function allRoles( $request ) {

        $role = RoleModel::select( 'roles.*' );

        $filterObject = self::filter( $request, $role );
        $role = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $role->orderBy( 'created_at', $dir );
                    break;
                case 2:
                    $role->orderBy( 'name', $dir );
                    break;
            }
        }

        $roleCount = $role->count();

        $limit = $request->length;
        $offset = $request->start;
        
        $roles = $role->skip( $offset )->take( $limit )->get();

        $totalRecord = RoleModel::count();

        $data = [
            'roles' => $roles,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $roleCount : $totalRecord,
            'recordsTotal' => $totalRecord,
        ];

        return response()->json( $data );
    }

    private function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $search_date = $request->input( 'columns.1.search.value' ) ) ) {
            if ( str_contains( $search_date, 'to' ) ) {
                $dates = explode( ' to ', $search_date );
                $role->whereBetween( 'roles.created_at', [ $dates[0] . ' 00:00:00' , $dates[1] . ' 23:59:59' ] );
            } else {
                $role->whereBetween( 'roles.created_at', [ $search_date . ' 00:00:00' , $search_date . ' 23:59:59' ] );
            }
            $filter = true;
        }
        
        if ( !empty( $name = $request->input( 'columns.2.search.value' ) ) ) {
            $role->where( 'name', $name );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];        
    }

    public function oneRole( $request ) {

        $permission = \DB::table( 'role_has_permissions' )->where( 'role_id', $request->id )->leftJoin( 'permissions', 'role_has_permissions.permission_id', '=', 'permissions.id' )->get();

        return response()->json( [ 'role' => RoleModel::find( $request->input( 'id' ) ), 'permissions' => $permission ] );
    }

    public function createRole( $request ) {

        $request->validate( [
            'role_name' => 'required|unique:roles,name',
            'guard_name' => 'required',
        ] );

        $role = Role::create( [ 'name' => $request->role_name, 'guard_name' => $request->guard_name ] );

        if ( !empty( $request->modules ) ) {
            foreach( $request->modules as $key => $module ) {

                $key = explode( '|', $key );
                if ( $key[1] != $roleModel->guard_name ) {
                    echo $key[1];
                    continue;
                }

                foreach( $module as $action ) {
                    $role->givePermissionTo( $action . ' ' . $key[0] );
                }
            }
        }
    }

    public function updateRole( $request ) {

        $roleModel = RoleModel::find( $request->input( 'id' ) );
        $role = Role::findByName( $roleModel->name, $roleModel->guard_name );

        $permissions = [];
        
        if ( $request->modules ) {
            foreach( $request->modules as $key => $module ) {
                $key = explode( '|', $key );
                if ( $key[1] != $roleModel->guard_name ) {
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

    public function delete() {
        

    }
}