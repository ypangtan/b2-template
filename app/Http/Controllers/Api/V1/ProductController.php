<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    ProductService,
};

class ProductController extends Controller
{
    /**
     * 1. Get products
     * 
     * <aside class="warning">Please build the url based on Query Parameters required instead of writing all Query Parameters to the url.</aside>
     * 
     * <strong>sort</strong><br>
     * 1: price<br>
     * 
     * <strong>order</strong><br>
     * asc: Ascending<br>
     * desc: Descending<br>
     * 
     * @group Product API
     * 
     * @queryParam category string The category url_slug or ID to filter. Example: iphone
     * @queryParam sort integer Sort collection by type. Example: 1
     * @queryParam order string Sort collection by order type. Example: asc
     * @queryParam per_page integer Retrieve how many product in a page, default is 10. Example: 10
     * 
     */ 
    public function getProducts( Request $request ) {

        return ProductService::getProducts( $request );
    }

    /**
     * 6. Get product
     * 
     * <aside class="warning">Please build the url based on Query Parameters required instead of writing all Query Parameters to the url.</aside>
     * 
     * Either one is required only.
     * 
     * @group Product API
     * 
     * @queryParam url_slug string required The product url_slug to filter. Example: iphone
     * @queryParam id string required The product ID to filter. Example: 1
     * 
     */ 
    public function getProduct( Request $request ) {

        return ProductService::getProduct( $request );
    }
}
