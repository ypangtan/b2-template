<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    DashboardService
};

class DashboardController extends Controller
{
    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.dashboard' );
        $this->data['content'] = 'branch.dashboard.index';

        $this->data['data'] = DashboardService::dashboardDatas( $request );    
        
        // var_dump( $this->data['data'] );

        return view( 'branch.main' )->with( $this->data );
    }
}
