<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if( Session()->has( 'appLocale' ) && array_key_exists( Session()->get( 'appLocale' ), config( 'languages' ) ) ) {
            App::setLocale( Session()->get( 'appLocale' ) );
        } else {
            App::setLocale( config( 'app.fallback_locale' ) );
        }
        return $next( $request );
    }
}
