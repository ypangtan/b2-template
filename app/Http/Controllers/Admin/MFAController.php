<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    MFAService,
};

use Helper;

use PragmaRX\Google2FAQRCode\Google2FA;

class MFAController extends Controller
{
    public function firstSetup( Request $request ) {

        if ( !empty( auth()->user()->mfa_secret ) ) {
            return redirect()->route( 'admin.dashboard' );
        }

        $this->data['header']['title'] = __( 'template.first_setup' );
        
        $this->data['content'] = 'admin.setting.first_setup';

        $google2fa = new Google2FA();

        $secretKey = $google2fa->generateSecretKey();

        $qrCodeUrl = $google2fa->getQRCodeInline(
            Helper::websiteName(),
            auth()->user()->email,
            $secretKey
        );

        $this->data['data']['mfa_qr'] = $qrCodeUrl;
        $this->data['data']['mfa_secret'] = $secretKey;

        return view( 'admin.main_blank' )->with( $this->data );
    }

    public function verify( Request $request ) {

        $value = $request->session()->get( 'mfa-ed' );

        if ( $value ) {
            return redirect()->route( 'admin.dashboard' );
        }
        
        $this->data['header']['title'] = __( 'template.verify_account' );
        
        $this->data['content'] = 'admin.administrator.verify';

        return view( 'admin.main_blank' )->with( $this->data );
    }

    public function setupMFA( Request $request ) {

        return MFAService::setupMFA( $request );
    }

    public function verifyCode( Request $request ) {

        return MFAService::verifyCode( $request );
    }
}
