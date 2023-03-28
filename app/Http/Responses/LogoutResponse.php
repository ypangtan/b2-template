<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function toResponse($request)
    {
        if( request()->is( 'backoffice/*' ) ) {
            return $request->wantsJson() ? new JsonResponse( '', 204 ) : redirect( 'backoffice/login' );
        } else if( request()->is( 'base2_branch/*' ) ) {
            return $request->wantsJson() ? new JsonResponse( '', 204 ) : redirect( 'base2_branch/login' );
        } else {
            return $request->wantsJson() ? new JsonResponse( '', 204 ) : redirect( 'login' );
        }
    }
}