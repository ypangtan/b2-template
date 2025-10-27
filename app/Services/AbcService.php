<?php

namespace App\Services\AbcService;

use App\Models\{
    Abc,
};

use Helper;

use Carbon\Carbon;

class AbcServiceService {

    public static function allAbcs( $request ) {

        $abc = Abc::with( [
        ] )->select( 'abcs.*' );

        $filterObject = self::filter( $request, $abc );
        $abc = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $abc->orderBy( 'created_at', $dir );
                    break;
            }
        }

        $abcCount = $abc->count();

        $limit = $request->length;
        $offset = $request->start;

        $abcs = $abc->skip( $offset )->take( $limit )->get();

        $abcs->append( [
            'encrypted_id',
        ] );

        $data = [
            'abcs' => $abcs,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $abcCount : Abc::count(),
            'recordsTotal' => Abc::count(),
        ];

        return $data;
    }

    private static function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->registered_date ) ) {
            if ( str_contains( $request->registered_date, 'to' ) ) {
                $dates = explode( ' to ', $request->registered_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'abcs.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->registered_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'abcs.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'abcs.status', $request->status );
            $filter = true;
        }     

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneAbc( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );
        
        $abc = Abc::with( [
        ] )->find( $request->id );

        return $abc;
    }

    public static function createAbc( $request ) {

        DB::beginTransaction();

        $validator = Validator::make( $request->all(), [
            'name' => [ 'required' ],
        ] );

        $attributeName = [
            'name' => __( 'abc.name' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $createAbcObject['Abc'] = [
                'name' => $request->name,
                'status' => 10,
            ];
            $createAbc = Abc::create( $createAbcObject );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.new_x_created', [ 'title' => Str::singular( __( 'template.abcs' ) ) ] ),
        ] );
    }

    public static function updateAbc( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'name' => [ 'nullable' ],
        ] );

        $attributeName = [
            'name' => __( 'abc.name' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();    

        try {

            $updateAbc = Abc::lockForUpdate()
                ->find( $request->id );
            $updateAbc->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.abcs' ) ) ] ),
        ] );
    }

    public static function updateAbcStatus( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'status' => 'required',
        ] );
        
        $validator->validate();

        try {

            $updateAbc = Abc::lockForUpdate()->find( $request->id );
            $updateAbc->status = $request->status;
            $updateAbc->save();

            DB::commit();
            
            return response()->json( [
                'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.abcs' ) ) ] ),
            ] );

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }
    }
}