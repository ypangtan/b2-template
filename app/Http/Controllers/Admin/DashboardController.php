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

    public function test99( Request $request ) {

        $google2fa = new Google2FA();

        $secretKey = $google2fa->generateSecretKey();
    
        $qrCodeUrl = $google2fa->getQRCodeInline(
            Helper::websiteName(),
            auth()->user()->email,
            $secretKey
        );

        echo $secretKey;

        return $qrCodeUrl;
    }

    public function test88( Request $request ) {

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey( $request->secret, $request->code );

        var_dump( $valid );
    }

    public function index( Request $request ) {

        $this->data['header']['title'] = __( 'template.dashboard' );
        $this->data['content'] = 'admin.dashboard.index';

        $this->data['data'] = DashboardService::dashboardDatas( $request );        

        return view( 'admin.main' )->with( $this->data );
    }

    public function totalDatas( Request $request ) {

        return DashboardService::totalDatas( $request );
    }

    public function monthlySales( Request $request ) {

        return DashboardService::monthlySales( $request );
    }
}