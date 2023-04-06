<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    Product,
};

use Helper;

use Carbon\Carbon;

class ProductService {

    public function allProducts( $request ) {

        $product = Product::select( 'products.*' );

        $filterObject = self::filter( $request, $product );
        $product = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 2:
                    $product->orderBy( 'created_at', $dir );
                    break;
                case 3:
                    $product->orderBy( 'title', $dir );
                    break;
                case 4:
                    $product->orderBy( 'email', $dir );
                    break;
            }
        }

        $productCount = $product->count();

        $limit = $request->length;
        $offset = $request->start;

        $products = $product->skip( $offset )->take( $limit )->get();

        if ( $products ) {
            $products->append( [
                'path',
                'encrypted_id'
            ] );
        }

        $totalRecord = Product::count();

        $data = [
            'products' => $products,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $productCount : $totalRecord,
            'recordsTotal' => $totalRecord,
        ];

        return response()->json( $data );
    }

    private function filter( $request, $model ) {

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

        if ( !empty( $request->title ) ) {
            $model->where( 'title', $request->title );
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
}