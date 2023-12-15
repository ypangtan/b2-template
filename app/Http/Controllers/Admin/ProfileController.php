<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    ProfileService,
};

use Helper;

use PragmaRX\Google2FAQRCode\Google2FA;

class ProfileController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.profile' );
        $this->data['content'] = 'admin.profile.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.profile' ),
            'title' => __( 'template.profile' ),
            'mobile_title' => __( 'template.profile' ),
        ];

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

    public function update( Request $request ) {

        return ProfileService::update( $request );
    }
}
