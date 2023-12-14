<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  $request
     * @return mixed
     */
    public function toResponse( $request )
    {
        switch ( $request->route()->getName() ) {
            case 'admin._login':
                return redirect()->intended( route( 'admin.dashboard.index' ) );
                break;
            case 'web._login':
                return redirect()->intended( route( 'web.home' ) );
                break;
        }
    }
}