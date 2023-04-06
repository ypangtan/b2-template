<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.orders' );
        $this->data['content'] = 'admin.order.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.orders' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.orders' ),
        ];

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function allOrders() {

    }

    public function oneOrder() {

    }

    public function createOrder() {

    }

    public function updateOrder() {

    }

    public function updateOrderStatus() {
        
    }
}
