<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\{
    Metadata,
    Product,
    ProductImage,
    ProductInventory,
    ProductPrice,
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

    public function oneProduct( $request ) {

        $product = Product::with( [ 
            'metadata',
            'productImages', 
            'productInventory', 
            'productPrices' 
        ] )->find( Helper::decode( $request->id ) );

        if ( $product ) {
            $product->append( [
                'encrypted_id',
            ] );

            if ( $product->productPrices ) {
                $product->productPrices->append( [
                    'promo_enabled',
                ] );
            }
        }

        return response()->json( $product );
    }

    public function createProduct( $request ) {

        $request->validate( [
            'sku' => [ 'required' ],
            'title' => [ 'required' ],
            'short_description' => [ 'required' ],
            'regular_price' => [ 'bail', 'required', 'numeric', 'min:0.01', 'regex:/^\d*(\.\d{2})?$/' ],
            'taxable' => [ 'required' ],
            'enable_promotion' => [ 'required' ],
            'promo_price' => [ 'exclude_if:enable_promotion,no', 'bail', 'numeric', 'min:0.01', 'lt:regular_price', 'regex:/^\d*(\.\d{2})?$/' ],
            'promo_date_from' => [ 'exclude_if:enable_promotion,no', 'required', 'before:promo_date_to' ],
            'promo_date_to' => [ 'exclude_if:enable_promotion,no', 'required', 'after:promo_date_from' ],
            'quantity' => [ 'required', 'integer', 'min:0' ],
            'friendly_url' => [ 'required', 'unique:products,url_slug' ],
            'meta_title' => [ 'required' ],
            'meta_description' => [ 'required' ],
        ] );

        $basicAttribute = [
            'sku' => $request->sku,
            'title' => $request->title,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'url_slug' =>  Str::slug( $request->friendly_url ? $request->friendly_url : $request->title ),
            'type' => 'single',
            'status' => 10,
        ];

        $createProduct = Product::create( $basicAttribute );

        $priceAttribute = [
            'product_id' => $createProduct->id,
            'regular_price' => $request->regular_price,
        ];

        if ( $request->enable_promotion ) {
            $priceAttribute['promo_price'] = $request->promo_price;
            $priceAttribute['promo_date_from'] = $request->promo_date_from;
            $priceAttribute['promo_date_to'] = $request->promo_date_to;
        }

        $createProductPrice = ProductPrice::create( $priceAttribute );

        $createProductInventory = ProductInventory::create( [
            'product_id' => $createProduct->id,
            'quantity' => $request->quantity,
        ] );

        Metadata::create( [
            'type' => 'product',
            'type_id' => $createProduct->id,
            'key' => 'meta_title',
            'value' => $request->meta_title,
        ] );

        Metadata::create( [
            'type' => 'product',
            'type_id' => $createProduct->id,
            'key' => 'meta_description',
            'value' => $request->meta_description,
        ] );
    }
}