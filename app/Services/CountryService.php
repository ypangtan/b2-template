<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Validator,
};

use App\Models\{
    Country,
};

use Carbon\Carbon;
use Helper;

class CountryService {

    public static function allCountries( $request ) {

        $country = Country::select( 'countries.*' );

        $filterObject = self::filterCountry( $request, $country );
        $country = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 2:
                    $country->orderBy( 'created_at', $dir );
                    break;
                case 3:
                    $country->orderBy( 'name', $dir );
                    break;
            }
        }

        $countryCount = $country->count();

        $limit = $request->length;
        $offset = $request->start;

        $countries = $country->skip( $offset )->take( $limit )->get();

        $countries->append( [
            'encrypted_id'
        ] );

        $country = Country::select(
            DB::raw( 'COUNT(countries.id) as total'
        ) );

        $filterObject = self::filterCountry( $request, $country );
        $country = $filterObject['model'];
        $filter = $filterObject['filter'];

        $country = $country->first();

        $data = [
            'countries' => $countries,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $countryCount : $country->total,
            'recordsTotal' => $filter ? Country::count() : $countryCount,
        ];

        return response()->json( $data );
    }

    private static function filterCountry( $request, $model ) {

        $filter = false;

        if ( !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->created_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $request->country_name ) ) {
            $model->where( 'country_name', 'LIKE', '%' . $request->country_name . '%' );
            $filter = true;
        }
        
        if ( !empty( $request->country ) ) {
            $model->where( 'country_name', 'LIKE', '%' . $request->country . '%' )
                ->orWhere( 'iso_alpha2_code', 'LIKE', '%' . $request->country . '%' )
                ->orWhere( 'iso_alpha3_code', 'LIKE', '%' . $request->country . '%' );
            $filter = true;
        }
        
        if ( !empty( $request->status ) ) {
            $model->where( 'status', $request->status );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneCountry( $request ) {

        $country = Country::find( Helper::decode( $request->id ) );

        if ( $country ) {
            $country->append( [
                'encrypted_id',
            ] );
        }

        return response()->json( $country );
    }

    public static function updateCountry ( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'country_name' => [ 'required' ],
            'country_image' => [ 'required' ],
            'country_icon' => [ 'required' ],
            'currency_name' => [ 'required' ],
            'currency_symbol' => [ 'required' ],
            'iso_currency' => [ 'required' ],
            'iso_alpha2_code' => [ 'required' ],
            'iso_alpha3_code' => [ 'nullable' ],
            'call_code' => [ 'required' ],
        ] );

        $attributeName = [
            'country_name' => __( 'country.country_name' ),
            'country_image' => __( 'country.country_image' ),
            'country_icon' => __( 'country.country_icon' ),
            'currency_name' => __( 'country.currency_name' ),
            'currency_symbol' => __( 'country.currency_symbol' ),
            'iso_currency' => __( 'country.iso_currency' ),
            'iso_alpha2_code' => __( 'country.iso_alpha2_code' ),
            'iso_alpha3_code' => __( 'country.iso_alpha3_code' ),
            'call_code' => __( 'country.call_code' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();    

        try {

            $country = Country::lockForUpdate()->find( $request->id );
            $country->country_name = $request->country_name;
            $country->country_image = $request->country_image;
            $country->country_icon = $request->country_icon;
            $country->currency_name = $request->currency_name;
            $country->currency_symbol = $request->currency_symbol;
            $country->iso_currency = $request->iso_currency;
            $country->iso_alpha2_code = $request->iso_alpha2_code;
            $country->iso_alpha3_code = $request->iso_alpha3_code;
            $country->call_code = $request->call_code;
            $country->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.countries' ) ) ] ),
        ] );
    }

    public static function updateCountryStatus( $request ) {

        if( !empty( $request->id ) ) {
            $request->merge( [
                'id' => \Helper::decode( $request->id )
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'status' => [ 'required', 'in:10,20' ],
        ] );

        $attributeName = [
            'status' => __( 'template.status' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            DB::beginTransaction();

            $country = Country::lockForUpdate()->find( $request->id );
            $country->status = $request->status;
            $country->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.countries' ) ) ] ),
        ] );
    }

    public static function updateCountryStatusMultiple( $request ) {

        $validator = Validator::make( $request->all(), [
            'status' => [ 'required', 'in:10,20' ],
        ] );

        $attributeName = [
            'status' => __( 'template.status' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            foreach ( $request->ids as $id ) {

                DB::beginTransaction();
                $country = Country::lockForUpdate()->find( \Helper::decode( $id ) );
                $country->status = $request->status;
                $country->save();

                DB::commit();
            }

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.countries' ) ) ] ),
        ] );
    }

    public static function getCountries( $request ) {

        $countries = Country::where( 'status', 10 )
            ->when( $request->name != '', function( $query ) use ( $request ) {
                $query->where( 'country_name', 'LIKE', '%' . $request->name . '%' );
            } )
            ->orderBy( 'country_name', 'ASC' )
            ->get();

        return response()->json( [
            'data' => $countries,
        ] );
    }
}