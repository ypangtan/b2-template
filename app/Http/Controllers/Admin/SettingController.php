<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    SettingService
};

use Helper;

use PragmaRX\Google2FAQRCode\Google2FA;

class SettingController extends Controller {

    public function index() {

        $this->data['header']['title'] = __( 'template.settings' );

        $this->data['content'] = 'admin.setting.index';

        $google2fa = new Google2FA();

        $secretKey = $google2fa->generateSecretKey();

        $qrCodeUrl = $google2fa->getQRCodeInline(
            Helper::websiteName(),
            auth()->user()->email,
            $secretKey
        );

        $this->data['data']['mfa_qr'] = $qrCodeUrl;
        $this->data['data']['mfa_secret'] = $secretKey;

        return view( 'admin.main' )->with( $this->data );
    }

    public function resetMFA( Request $request ) {

        return SettingService::resetMFA( $request );
    }

    public function setupMFA( Request $request ) {

        return SettingService::setupMFA( $request );
    }
}