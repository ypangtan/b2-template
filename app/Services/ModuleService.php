<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Models\Permission;

use App\Models\{
    Module
};

use Helper;

class ModuleService {

    public static function all( $request ) {

        $filter = false;

        $limit = $request->input( 'length' );
        $offset = $request->input( 'start' );

        $module = Module::select( 'modules.*' );

        if( !empty( $search_date = $request->input( 'columns.1.search.value' ) ) ) {
            if( str_contains( $search_date, 'to' ) ) {
                $dates = explode( ' to ', $search_date );
                $module->whereBetween( 'modules.created_at', [ $dates[0] . ' 00:00:00' , $dates[1] . ' 23:59:59' ] );
            } else {
                $module->whereBetween( 'modules.created_at', [ $search_date . ' 00:00:00' , $search_date . ' 23:59:59' ] );
            }
            $filter = true;
        }
        
        if( !empty( $name = $request->input( 'columns.2.search.value' ) ) ) {
            $module->where( 'name', 'LIKE', "%{$name}%" );
            $filter = true;
        }

        if( $request->input( 'order.0.column' ) != 0 ) {

            switch( $request->input( 'order.0.column' ) ) {
                case 1:
                    $module->orderBy( 'created_at', $request->input( 'order.0.dir' ) );
                    break;
                case 2:
                    $module->orderBy( 'name', $request->input( 'order.0.dir' ) );
                    break;
            }
        }

        $count_module = $module->count();
        
        $modules = $module->skip( $offset )->take( $limit )->get();

        $total = Module::count();

        $data = array(
            'modules' => $modules,
            'draw' => $request->input( 'draw' ),
            'recordsFiltered' => $filter ? $count_module : $total,
            'recordsTotal' => $total,
        );

        return $data;
    }

    public static function create( $request ) {

        $request->validate( [
            'module_name' => 'required|max:50',
            'guard_name' => 'required',
        ] );

        Module::create( [
            'name' => $request->module_name,
            'guard_name' => $request->guard_name,
        ] );

        foreach( Helper::moduleActions() as $action ) {
            Permission::create( [ 'name' => $action . ' ' . $request->module_name, 'guard_name' => $request->guard_name ] );
        }
    }
}