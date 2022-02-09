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
        $home = request()->is( 'base2_admin/*' ) ? '/base2_admin/dashboard' : '/home';

        return redirect()->intended( $home );
    }
}