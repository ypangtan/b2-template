<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    CategoryService,
};

class CategoryController extends Controller
{
    public function index() {
        
        $this->data['header']['title'] = __( 'template.categories' );
        $this->data['content'] = 'admin.category.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.categories' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.categories' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function add() {

        $this->data['header']['title'] = __( 'template.categories' );
        $this->data['content'] = 'admin.category.add';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.categories' ),
            'title' => __( 'template.add' ),
            'mobile_title' => __( 'template.categories' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function edit() {

        $this->data['header']['title'] = __( 'template.categories' );
        $this->data['content'] = 'admin.category.edit';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.categories' ),
            'title' => __( 'template.edit' ),
            'mobile_title' => __( 'template.categories' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function allCategories( Request $request ) {

        return CategoryService::allCategories( $request );
    }

    public function oneCategory( Request $request ) {

        return CategoryService::oneCategory( $request );
    }

    public function createCategory( Request $request ) {

        return CategoryService::createCategory( $request );
    }

    public function updateCategory( Request $request ) {

        return CategoryService::updateCategory( $request );
    }

    public function updateCategoryStatus( Request $request ) {
        
    }

    public function getCategoryStructure( Request $request ) {

        return CategoryService::getCategoryStructure( $request );
    }
}
