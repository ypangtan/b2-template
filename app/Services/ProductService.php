<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    App,
    DB,
    Storage,
    Validator,
};
use Illuminate\Support\Str;

use App\Models\{
    Category,
    Metadata,
    Product,
    ProductCategory,
    ProductImage,
    ProductInventory,
    ProductPrice,
};

use Helper;

use Carbon\Carbon;

class ProductService {

    public static function allProducts( $request ) {

        $product = Product::with( [ 'productImages' ] )->select( 'products.*' );

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

            foreach( $products as $p ) {
                if ( $p->productImages ) {
                    $p->productImages->append( [
                        'path'
                    ] );
                }
            }
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
            $model->where( 'title', 'LIKE', '%' . $request->title . '%' );
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

    public static function oneProduct( $request ) {

        $product = Product::with( [ 
            'metadata',
            'productCategories',
            'productImages', 
            'productInventory', 
            'productPrices' 
        ] )->find( Helper::decode( $request->id ) );

        if ( $product ) {
            $product->append( [
                'encrypted_id',
            ] );

            if ( $product->productImages ) {
                $product->productImages->append( [
                    'path',
                ] );
            }
        }

        $data['product'] = $product;
        $data['categories'] = CategoryService::getCategoryStructure( $request );

        return response()->json( $data );
    }

    public static function createProduct( $request ) {

        $validator = Validator::make( $request->all(), [
            'sku' => [ 'required', 'unique:products,sku' ],
            'title' => [ 'required' ],
            'short_description' => [ 'required' ],
            // 'description' => [ 'required' ],
            'regular_price' => [ 'bail', 'required', 'numeric', 'min:0.01', 'regex:/^\d*(\.\d{2})?$/' ],
            'taxable' => [ 'required' ],
            'enable_promotion' => [ 'required' ],
            'promo_price' => [ 'exclude_if:enable_promotion,0', 'bail', 'numeric', 'min:0.01', 'lt:regular_price', 'regex:/^\d*(\.\d{2})?$/' ],
            'promo_date_from' => [ 'exclude_if:enable_promotion,0', 'required', 'before:promo_date_to' ],
            'promo_date_to' => [ 'exclude_if:enable_promotion,0', 'required', 'after:promo_date_from' ],
            'quantity' => [ 'required', 'integer', 'min:0' ],
            'images' => [ 'required', 'array' ],
            'images.*' => [ 'mimetypes:image/*' ],
            'friendly_url' => [ 'required', 'unique:products,url_slug' ],
            'meta_title' => [ 'required' ],
            'meta_description' => [ 'required' ],
        ] );

        $attributeName = [
            'sku' => __( 'product.sku' ),
            'title' => __( 'datatables.title' ),
            'short_description' => __( 'template.short_description' ),
            'regular_price' => __( 'product.regular_price' ),
            'promo_price' => __( 'product.promo_price' ),
            'promo_date_from' => __( 'product.promo_date_from' ),
            'promo_date_to' => __( 'product.promo_date_to' ),
            'quantity' => __( 'product.quantity' ),
            'preloaded' => __( 'product.preloaded' ),
            'images' => __( 'template.gallery' ),
            'friendly_url' => __( 'template.friendly_url' ),
            'meta_title' => __( 'template.meta_title' ),
            'meta_description' => __( 'template.meta_description' ),
        ];

        $validator->setAttributeNames( $attributeName )->validate();

        $basicAttribute = [
            'sku' => $request->sku,
            'title' => [
                'en' => $request->title,
                App::getLocale() => $request->title,
            ],
            'short_description' => [
                'en' => $request->short_description,
                App::getLocale() => $request->short_description,
            ],
            'description' => [
                'en' => $request->description,
                App::getLocale() => $request->description,
            ],
            'url_slug' => Str::slug( $request->friendly_url ? $request->friendly_url : $request->title ),
            'type' => 'single',
            'status' => 10,
        ];

        DB::beginTransaction();

        try {

            $createProduct = Product::create( $basicAttribute );

            $priceAttribute = [
                'product_id' => $createProduct->id,
                'display_price' => $request->regular_price,
                'regular_price' => $request->regular_price,
                'promo_enabled' => $request->enable_promotion,
            ];

            if ( $request->enable_promotion == 1 ) {
                $priceAttribute['display_price'] = $request->promo_price;
                $priceAttribute['promo_price'] = $request->promo_price;
                $priceAttribute['promo_date_from'] = $request->promo_date_from;
                $priceAttribute['promo_date_to'] = $request->promo_date_to;
            }

            $createProductPrice = ProductPrice::create( $priceAttribute );

            $createProductInventory = ProductInventory::create( [
                'product_id' => $createProduct->id,
                'quantity' => $request->quantity,
            ] );

            if ( $request->hasFile( 'images' ) ) {
                foreach( $request->file( 'images' ) as $image ) {
                    $createProductImage = ProductImage::create( [
                        'product_id' => $createProduct->id,
                        'title' => $image->getClientOriginalName(),
                        'file' => $image->store( 'products/' . $createProduct->id, [ 'disk' => 'public' ] ),
                        'type' => 1,
                        'file_type' => 2, // 1: pdf 2: image
                    ] );
                }
            }

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

            ProductCategory::where( 'product_id', $createProduct->id )->delete();
            $categories = json_decode( $request->categories );
            foreach ( $categories as $cid ) {
                $cid = str_replace( 'child_', '', $cid );

                $category = Category::find( $cid );
                $structure = $category->structure . '|' . $cid;
                $parents = array_reverse( explode( '|', $structure ) );
                foreach ( $parents as $parent ) {
                    if ( $parent != '-' ) {
                        ProductCategory::updateOrCreate( [
                            'product_id' => $createProduct->id,
                            'category_id' => $parent,
                            'is_child' => $parent == $cid ? 1 : 0,
                            'status' => 10,
                        ] );
                    }
                }
            }

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ] );
        }

        return response()->json( [
            'message' => __( 'product.product_created' ),
        ] );
    }

    public static function updateProduct( $request ) {

        $request->merge( [
            'decrypted_id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'sku' => [ 'required', 'unique:products,sku,' . $request->decrypted_id ],
            'title' => [ 'required' ],
            'short_description' => [ 'required' ],
            // 'description' => [ 'required' ],
            'regular_price' => [ 'bail', 'required', 'numeric', 'min:0.01', 'regex:/^\d*(\.\d{2})?$/' ],
            'taxable' => [ 'required' ],
            'enable_promotion' => [ 'required' ],
            'promo_price' => [ 'exclude_if:enable_promotion,0', 'bail', 'numeric', 'min:0.01', 'lt:regular_price', 'regex:/^\d*(\.\d{2})?$/' ],
            'promo_date_from' => [ 'exclude_if:enable_promotion,0', 'required', 'before:promo_date_to' ],
            'promo_date_to' => [ 'exclude_if:enable_promotion,0', 'required', 'after:promo_date_from' ],
            'quantity' => [ 'required', 'integer', 'min:0' ],
            'images' => [ 'required_without:preloaded', 'array' ],
            'images.*' => [ 'mimetypes:image/*' ],
            'friendly_url' => [ 'required', 'unique:products,url_slug,' . $request->decrypted_id ],
            'meta_title' => [ 'required' ],
            'meta_description' => [ 'required' ],
        ] );

        $attributeName = [
            'sku' => __( 'product.sku' ),
            'title' => __( 'datatables.title' ),
            'short_description' => __( 'template.short_description' ),
            'regular_price' => __( 'product.regular_price' ),
            'promo_price' => __( 'product.promo_price' ),
            'promo_date_from' => __( 'product.promo_date_from' ),
            'promo_date_to' => __( 'product.promo_date_to' ),
            'quantity' => __( 'product.quantity' ),
            'preloaded' => __( 'product.preloaded' ),
            'images' => __( 'template.gallery' ),
            'friendly_url' => __( 'template.friendly_url' ),
            'meta_title' => __( 'template.meta_title' ),
            'meta_description' => __( 'template.meta_description' ),
        ];

        $validator->setAttributeNames( $attributeName )->validate();

        $updateProduct = Product::find( $request->decrypted_id );
        $updateProduct->sku = $request->sku;
        $updateProduct->title = [
            App::getLocale() => $request->title,
        ];
        $updateProduct->description = [
            App::getLocale() => $request->description,
        ];
        $updateProduct->short_description = [
            App::getLocale() => $request->short_description,
        ];
        $updateProduct->url_slug = Str::slug( $request->friendly_url ? $request->friendly_url : $request->title );
        $updateProduct->save();

        $updateProductPrice = ProductPrice::where( 'product_id', $updateProduct->id )->first();
        $updateProductPrice->display_price = $request->regular_price;
        $updateProductPrice->regular_price = $request->regular_price;
        $updateProductPrice->promo_enabled = $request->enable_promotion;
        if ( $request->enable_promotion == 1 ) {
            $updateProductPrice->display_price = $request->promo_price;
            $updateProductPrice->promo_price = $request->promo_price;
            $updateProductPrice->promo_date_from = $request->promo_date_from;
            $updateProductPrice->promo_date_to = $request->promo_date_to;
        }
        $updateProductPrice->save();

        if ( isset( $request->preloaded ) ) {
            $toBeDelete = ProductImage::where( 'product_id', $updateProduct->id )->whereNotIn( 'id', $request->preloaded );
            foreach( $toBeDelete->get() as $tbd ) {
                Storage::disk( 'public' )->delete( $tbd->file );
            }
            $toBeDelete->delete();
        } else {
            $toBeDelete = ProductImage::where( 'product_id', $updateProduct->id );
            foreach( $toBeDelete->get() as $tbd ) {
                Storage::disk( 'public' )->delete( $tbd->file );
            }
            $toBeDelete->delete();
        }

        if ( $request->hasFile( 'images' ) ) {
            foreach( $request->file( 'images' ) as $image ) {
                $createProductImage = ProductImage::create( [
                    'product_id' => $updateProduct->id,
                    'title' => $image->getClientOriginalName(),
                    'file' => $image->store( 'products/' . $updateProduct->id, [ 'disk' => 'public' ] ),
                    'type' => 1,
                    'file_type' => 2, // 1: pdf 2: image
                ] );
            }
        }

        ProductCategory::where( 'product_id', $updateProduct->id )->delete();
        $categories = json_decode( $request->categories );
        foreach ( $categories as $cid ) {
            $cid = str_replace( 'child_', '', $cid );

            $category = Category::find( $cid );
            $structure = $category->structure . '|' . $cid;
            $parents = array_reverse( explode( '|', $structure ) );
            foreach ( $parents as $parent ) {
                if ( $parent != '-' ) {
                    ProductCategory::updateOrCreate( [
                        'product_id' => $updateProduct->id,
                        'category_id' => $parent,
                        'is_child' => $parent == $cid ? 1 : 0,
                        'status' => 10,
                    ] );
                }
            }
        }

        Metadata::updateOrCreate( [
            'type' => 'product',
            'type_id' => $updateProduct->id,
            'key' => 'meta_title',
        ], [
            'value' => $request->meta_title,
        ] );

        Metadata::updateOrCreate( [
            'type' => 'product',
            'type_id' => $updateProduct->id,
            'key' => 'meta_description',
        ], [
            'value' => $request->meta_description,
        ] );

        return response()->json( [
            'message' => __( 'product.product_updated' ),
        ] );
    }

    public static function ckeUpload( $request ) {

        $file = $request->file( 'file' )->store( 'products/ckeditor', [ 'disk' => 'public' ] );

        $data = [
            'url' => asset( 'storage/' . $file ),
        ];

        return response()->json( $data );
    }

    public static function getProducts( $request ) {

        $products = Product::with( [
            'productCategories:product_id,category_id,status',
            'productCategories.category',
            'productImages'
        ] )->select( 'products.*' );

        $products->withAggregate( 'productPrices', 'display_price' );
        $products->withAggregate( 'productPrices', 'regular_price' );
        $products->withAggregate( 'productPrices', 'promo_price' );
        $products->withAggregate( 'productPrices', 'promo_enabled' );
        $products->withAggregate( 'productPrices', 'promo_date_from' );
        $products->withAggregate( 'productPrices', 'promo_date_to' );

        if ( $request->category ) {
            $products->whereHas( 'productCategories.category', function( $query ) {
                $query->where( 'url_slug', request( 'category' ) );
                $query->orWhere( 'id', request( 'category' ) );
            } );
        }

        if ( $request->sort ) {
            $dir = $request->order ? $request->order : 'desc';
            switch ( $request->sort ) {   
                case 1:
                    $products->orderBy( 'product_prices_display_price', $dir );
                    break;
            }
        }

        $products = $products->paginate( empty( $request->per_page ) ? 10 : $request->per_page );

        foreach( $products->items() as $item ) {
            if ( $item->productImages ) {
                $item->productImages->append( [
                    'path',
                ] );
            }
        }

        return response()->json( $products );
    }

    public static function getProduct( $request ) {

        $product = Product::with( [ 'productPrices', 'productImages' ] );
        $product->where( function( $query ) {
            $query->where( 'url_slug', request( 'url_slug' ) );
            $query->orWhere( 'id', request( 'id' ) );
        } );

        $product = $product->first();

        if ( $product ) {
            if ( $product->productImages ) {
                $product->productImages->append( [
                    'path',
                ] );
            }
        }

        return response()->json( $product );
    }
}