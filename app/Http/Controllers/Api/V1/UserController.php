<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Crypt,
    Hash,
    Http,
    Storage
};

use App\Models\{
    User,
};

use Helper;

class UserController extends Controller {

    public function __construct() {}

    public function login( Request $request ) {

        $request->validate( [
            'metamask_address' => 'required',
        ] );

        $user = User::where( 'metamask_address', $request->metamask_address )->firstOr( function() use( $request ) {

            return User::create( [
                'metamask_address' => $request->metamask_address,
            ] );

        } );

        if( $user ) {
            return response()->json( [ 'token' => $user->createToken( 'akc_web' )->plainTextToken ] );
        }

        return response()->json( [ 'message' => 'Unable to create user token' ], 500 );
    }
}