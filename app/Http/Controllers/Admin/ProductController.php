<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    ProductService,
};

class ProductController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.products' );
        $this->data['content'] = 'admin.product.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.products' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.products' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function add() {

        $this->data['header']['title'] = __( 'template.products' );
        $this->data['content'] = 'admin.product.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.products' ),
            'title' => __( 'template.add' ),
            'mobile_title' => __( 'template.products' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function edit() {

        $this->data['header']['title'] = __( 'template.products' );
        $this->data['content'] = 'admin.product.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.products' ),
            'title' => __( 'template.edit' ),
            'mobile_title' => __( 'template.products' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function allProducts( Request $request ) {

        return ProductService::allProducts( $request );
    }

    public function oneProduct( Request $request ) {

        return ProductService::oneProduct( $request );
    }

    public function createProduct( Request $request ) {

        return ProductService::createProduct( $request );
    }

    public function updateProduct( Request $request ) {

        return ProductService::updateProduct( $request );
    }

    public function updateProductStatus( Request $request ) {
 
        return ProductService::updateProductStatus( $request );
    }
}
