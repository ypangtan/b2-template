<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Validator,
};

use App\Models\{
    Administrator,
};

use App\Rules\CheckASCIICharacter;

use Helper;

use Carbon\Carbon;

class ProfileService {

    public function update( $request ) {

        $adminID = auth()->user()->id;

        $validator = Validator::make( $request->all(), [
            'username' => [ 'required', 'max:25', 'unique:administrators,username,' . $adminID, 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'max:25', 'unique:administrators,email,' . $adminID, 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
        ] );

        $attributeName = [
            'username' => __( 'administrator.username' ),
            'email' => __( 'administrator.email' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        DB::beginTransaction();
        
        try {

            $updateAdmin = Administrator::find( $adminID );
            $updateAdmin->username = $request->username;
            $updateAdmin->email = $request->email;
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