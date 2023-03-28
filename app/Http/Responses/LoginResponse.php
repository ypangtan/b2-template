<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $home = '/home';
        if( request()->is( 'backoffice/*' ) ) {
            $home = '/backoffice/dashboard';
        }
        if( request()->is( 'base2_branch/*' ) ) {
            $home = '/base2_branch/dashboard';
        }

        if( \Session::get( 'redirect' ) ) {
            $home = \Session::get( 'redirect' );
        }

        return redirect()->intended( $home );
    }
}