<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    Admin,
};

use PragmaRX\Google2FAQRCode\Google2FA;

class SettingService {

    public function resetMFA( $request ) {

        $request->validate( [
            'one_time_password' => [ 'bail', 'required', 'numeric', 'digits:6', function( $attributes, $value, $fail ) {
               
                $google2fa = new Google2FA();

                $valid = $google2fa->verifyKey( auth()->user()->mfa_secret, $value );
                if( !$valid ) {
                    $fail( __( 'setting.invalid_one_time_password' ) );
                }
            } ],
        ] );

        $admin = Admin::find( auth()->user()->id );
        $admin->mfa_secret = null;
        $admin->save();
        
        return true;
    }

    public function setupMFA( $request ) {

        $request->validate( [
            'one_time_password' => [ 'bail', 'required', 'numeric', 'digits:6', function( $attributes, $value, $fail ) {
               
                $google2fa = new Google2FA();

                $valid = $google2fa->verifyKey( request( 'mfa_secret' ), $value );
                if( !$valid ) {
                    $fail( __( 'setting.invalid_one_time_password' ) );
                }
            } ],
            'mfa_secret' => 'required',
        ] );

        $admin = Admin::find( auth()->user()->id );
        $admin->mfa_secret = $request->mfa_secret;
        $admin->save();

        return true;
    }
}