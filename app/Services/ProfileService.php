<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Hash,
    Validator,
};

use Illuminate\Validation\Rules\Password;

use App\Models\{
    Administrator,
};

use App\Rules\CheckASCIICharacter;

use Helper;

use Carbon\Carbon;

class ProfileService {

    public static function update( $request ) {

        DB::beginTransaction();

        $currentUser = auth()->user();
        $adminID = $currentUser->id;

        $validator = Validator::make( $request->all(), [
            'username' => [ 'required', 'unique:administrators,username,' . $adminID, 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:administrators,email,' . $adminID, 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'current_password' => [ 'nullable', function( $attribute, $value, $fail ) use ( $currentUser ) {
                if ( !empty( $value ) ) {
                    if ( !Hash::check( $value, $currentUser->password ) ) {
                        $fail( __( 'validation.current_password' ) );
                        return false;
                    }
                }
            } ],
            'new_password' => [ 'required_with:current_password', 'nullable', Password::min( 8 ) ],
            'confirm_new_password' => [ 'required_with:new_password', function( $attribute, $value, $fail ) use ( $request ) {
                if ( !empty( $value ) ) {
                    if ( $value != $request->new_password ) {
                        $fail( __( 'profile.confirm_new_password_not_match' ) );
                        return false;
                    }
                }
            } ],
        ] );

        $attributeName = [
            'username' => __( 'administrator.username' ),
            'email' => __( 'administrator.email' ),
            'current_password' => __( 'profile.current_password' ),
            'new_password' => __( 'profile.new_password' ),
            'confirm_new_password' => __( 'profile.confirm_new_password' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $updateAdmin = Administrator::find( $adminID );
            $updateAdmin->username = strtolower( $request->username );
            $updateAdmin->email = strtolower( $request->email );

            if ( !empty( $request->new_password ) ) {
                $updateAdmin->password = Hash::make( $request->new_password );
            }

            $updateAdmin->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.profile' ) ) ] ),
        ] );
    }
}