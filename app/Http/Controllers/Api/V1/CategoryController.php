<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    CategoryService,
};

class CategoryController extends Controller
{
     /**
     * 1. Get categories
     * 
     * <aside class="warning">Please build the url based on Query Parameters required instead of writing all Query Parameters to the url.</aside>
     * 
     * @group Category API
     * 
     * @queryParam url_slug string required The category url_slug to filter. Example: iphone-14-series
     * @queryParam id string required The category ID to filter. Example: 2
     * 
     */
    public function getCategories( Request $request ) {

        return CategoryService::getCategories( $request );
    }
}
