<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\{
    Hash,
    RateLimiter,
    Validator,
};

use Laravel\Fortify\Fortify;

use App\Models\{
    Administrator,
    ActivityLog,
    User,
};

use Helper;

Use Carbon\Carbon;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if( request()->is( 'backoffice/*' ) ) {
            config()->set( 'fortify.guard', 'admin' );
            config()->set( 'fortify.home', '/admin/home' );
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::authenticateUsing( function ( Request $request ) {

            if ( request()->is( 'backoffice/*' ) ) {

                $validator = Validator::make( $request->all(), [
                    'username' => [ 'required', function( $attribute, $value, $fail ) use ( $request, &$administrator ) {
                        $administrator = Administrator::where( 'email', $request->username )
                            ->where( 'status', 10 )
                            ->first();

                        if ( !$administrator || !Hash::check( $request->password, $administrator->password ) ) {
                            $fail( __( 'auth.failed' ) );
                        }
                    } ],
                    'password' => 'required',
                ] );

                $attributeName = [
                    'username' => __( 'administrator.email' ),
                ];
        
                foreach( $attributeName as $key => $aName ) {
                    $attributeName[$key] = strtolower( $aName );
                }
                
                $validator->setAttributeNames( $attributeName )->validate();
                
                return $administrator;

            } else {

                $request->validate( [
                    'username' => [ 'required', function( $attribute, $value, $fail ) use ( $request, &$user ) {
                        $user = User::where( 'username', $request->username )
                            ->where( 'status', 10 )
                            ->first();

                        if ( !$user || !Hash::check( $request->password, $user->password ) ) {
                            $fail( __( 'auth.failed' ) );
                        }
                    } ],
                    'password' => 'required',
                ] );

                return $user;
            }
        } );

        // Fortify::loginView( function () {
        //     return redirect()->route( 'admin.dashboard.index' );
        // } );

        RateLimiter::for('login', function (Request $request) {
            $identifier = empty( $request->email ) ? (string) $request->username : (string) $request->email;
            return Limit::perMinute(5)->by($identifier.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->app->singleton(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );
    }
}
