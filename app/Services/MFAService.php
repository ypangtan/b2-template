<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Storage,
    Validator,
};

use App\Models\{
    Administrator,
};

use PragmaRX\Google2FAQRCode\Google2FA;

class MFAService {

    public static function setupMFA( $request ) {

        DB::beginTransaction();

        $validator = Validator::make( $request->all(), [
            'code' => [ 'bail', 'required', 'digits:6', function( $attribute, $value, $fail ) use ( $request ) {

                $google2fa = new Google2FA();

                $valid = $google2fa->verifyKey( $request->mfa_secret, $value );
                if ( !$valid ) {
                    $fail( __( 'mfa.invalid_code' ) );
                }
            } ],
            'mfa_secret' => 'required',
        ] );

        $attributeName = [
            'code' => __( 'mfa.six_digit_code' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $updateAdministartor = Administrator::lockForUpdate()
                ->find( auth()->user()->id );

            $updateAdministartor->mfa_secret = Crypt::encryptString( $request->mfa_secret );
            $updateAdministartor->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'mfa.mfa_setup_complete' ),
        ] );
    }

    public static function verifyCode( $request ) {

        $request->validate( [
            'authentication_code' => [ 'bail', 'required', 'numeric', 'digits:6', function( $attribute, $value, $fail ) {

                $google2fa = new Google2FA();

                $secret = Crypt::decryptString( auth()->user()->mfa_secret );
                $valid = $google2fa->verifyKey( $secret, $value );
                if ( !$valid ) {
                    $fail( __( 'mfa.invalid_code' ) );
                }
            } ],
        ] );

        session( [
            'mfa-ed' => true
        ] );

        activity()
            ->useLog( 'administrators' )
            ->withProperties( [
                'attributes' => [
                    'new_login' => date( 'Y-m-d H:i:s' ),
                ]
            ] )
            ->log( 'admin login' );

        return response()->json( [
            'status' => true,
        ] );
    }
}