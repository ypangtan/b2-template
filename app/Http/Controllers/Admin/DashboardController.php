<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    DashboardService
};

use Helper;

use PragmaRX\Google2FAQRCode\Google2FA;

class DashboardController extends Controller {

    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.dashboard' );
        $this->data['content'] = 'admin.dashboard.index';

        $this->data['breadcrumbs'] = [
            'enabled' => false,
            'main_title' => __( 'template.dashboard' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.dashboard' ),
        ];

        $this->data['data'] = DashboardService::dashboardDatas( $request );

        return view( 'admin.main_v2' )->with( $this->data );
    }

    public function totalDatas( Request $request ) {

        return DashboardService::totalDatas( $request );
    }

    public function monthlySales( Request $request ) {

        return DashboardService::monthlySales( $request );
    }
}