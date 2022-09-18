<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Crypt,
    Hash,
    Http,
    Storage,
};
use App\Models\{
    ApiLog,
    Subscription,
    SubscriptionHistory,
    TmpUser,
    User,
    UserSocial,
    UserDeviceOneSignal
};

use Helper;

class UserService {

    public function requestOtp( $request ) {

        $request->validate( [
            'phone_number' => [ 'required', 'integer', function( $attributes, $value, $fail ) {
                // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
                $user = User::where( 'phone_number', $value )->first();
                if( $user ) {
                    $fail( __( 'api.phone_number_is_taken' ) );
                }
            } ],
        ] );

        $date = new \DateTime( date( 'Y-m-d H:i:s' ) );
        $date->add( new \DateInterval( 'PT15M' ) );

        \DB::beginTransaction();

        try {
            $createTmpUser = TmpUser::create( [
                'country_id' => 136,
                'phone_number' => $request->phone_number,
                'otp_code' => mt_rand( 100000, 999999 ),
                'status' => 'pending',
                'expire_on' => $date->format( 'Y-m-d H:i:s' ),
            ] );

            \DB::commit();

            // $response = Http::asForm()->post( 'https://ic1.silverstreet.com/send.php', [
            //     'username' => 'WashLa1',
            //     'password' => 'YxydpGhk1',
            //     'destination' => $createTmpUser->phone_number,
            //     'sender' => 'WashLa',
            //     'body' => 'Welcome to Big Mart. Your verification code is ' . $createTmpUser->otp_code . '. Valid for 15 minutes.',
            //     'bodytype' => 1,
            // ] );

            return response()->json( [
                'message' => $createTmpUser->phone_number,
                'data' => [
                    'otp_code' => '#DEBUG - ' . $createTmpUser->otp_code,
                    'tmp_user' => Crypt::encryptString( $createTmpUser->id ),
                ]
            ] );

        } catch ( \Throwable $th ) {

            \DB::rollBack();
            abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
        }
    }

    public function createUser( $request ) {

        $request->validate( [
            'otp_code' => 'required',
            'tmp_user' => [ 'required', function( $attributes, $value, $fail ) {

                $current = TmpUser::find( Crypt::decryptString( $value ) );

                if( $current->otp_code != request( 'otp_code' ) ) {
                    $fail( __( 'api.otp_code_invalid' ) );
                }

                $exist = TmpUser::where( 'phone_number', $current->phone_number )->where( 'status', 'registered' )->count();
                if( $exist > 1 ) {
                    $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                }
            } ],
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email|min:8|email|regex:/(.+)@(.+)\.(.+)/i',
            'password' => 'required|min:8',
            // 'country' => 'required|exists:countries,id',
            'phone_number' => [ 'required', 'integer', function( $attributes, $value, $fail ) {
                // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
                $user = User::where( 'phone_number', $value )->first();
                if( $user ) {
                    $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                }
            } ],
            'invitation_code' => 'sometimes|exists:users,invitation_code',
            'device_type' => 'required|in:1,2',
        ] );

        \DB::beginTransaction();

        try {

            $createUserObject = [
                'username' => $request->username,
                'email' => $request->email,
                'country_id' => 136,
                'phone_number' => $request->phone_number,
                'password' => Hash::make( $request->password ),
                'invitation_code' => strtoupper( \Str::random( 6 ) ),
            ];

            $referral = User::where( 'invitation_code', $request->invitation_code )->first();

            if( $referral ) {
                $createUserObject['referral_id'] = $referral->id;
                $createUserObject['referral_structure'] = $referral->referral_structure . '|' . $referral->id;
            } else {
                $createUserObject['referral'] = 0;
                $createUserObject['referral_structure'] = '-';
            }

            $createUser = User::create( $createUserObject );

            $updateTmpUser = TmpUser::find( Crypt::decryptString( $request->tmp_user ) );
            $updateTmpUser->status = 'registered';
            $updateTmpUser->save();

            // Register OneSignal
            if( !empty( $request->register_token ) ) {
                self::registerOneSignal( $createUser->id, $request->device_type, $request->register_token );
            }

            \DB::commit();

            return response()->json( [ 'data' => User::find( $createUser->id ), 'token' => $createUser->createToken( 'invictus_pro_app' )->plainTextToken ] );

        } catch ( \Throwable $th ) {

            \DB::rollBack();
            abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
        }
    }

    public function createToken( $request ) {

        $request->merge( [ 'account' => 'test' ] );

        $request->validate( [
            'username' => 'required',
            'password' => 'required',
            'account' => [ 'sometimes', function( $attributes, $value, $fail ) {

                $user = User::where( 'username', request( 'username' ) )->first();
                if( !$user ) {
                    $fail( __( 'api.user_wrong_user_password' ) );
                    return 0;
                }

                if( !Hash::check( request( 'password' ), $user->password ) ) {
                    $fail( __( 'api.user_wrong_user_password' ) );
                    return 0;
                }
            } ],
            'device_type' => 'required|in:1,2',
        ] );

        $user = User::where( 'username', $request->username )->first();

        // Register OneSignal
        if( !empty( $request->register_token ) ) {
            self::registerOneSignal( $user->id, $request->device_type, $request->register_token );
        }

        return response()->json( [ 'data' => $user, 'token' => $user->createToken( 'invictus_pro_app' )->plainTextToken ] );
    }

    public function createTokenSocial( $request ) {

        $request->validate( [
            'identifier' => [ 'required', function( $attributes, $value, $fail ) {
                $user = User::where( 'email', $value )->where( 'is_social_account', 0 )->first();
                if( $user ) {
                    $fail( __( 'api.email_is_taken_not_social' ) );
                }
                $userSocial = UserSocial::where( 'identifier', $value )->first();
                if( $userSocial ) {
                    if( $userSocial->platform != request('platform') ) {
                        $fail( __( 'api.email_is_taken_different_platform' ) );
                    }
                }
            } ],
            'platform' => 'required|in:1,2',
            'device_type' => 'required|in:1,2',
        ] );

        $userSocial = UserSocial::where( 'identifier', $request->identifier )->firstOr( function() use ( $request )  {

            \DB::beginTransaction();

            try {
                $createUser = User::create( [
                    'username' => null,
                    'email' => $request->identifier,
                    'country_id' => 136,
                    'phone_number' => null,
                    'is_social_account' => 1,
                    'invitation_code' => strtoupper( \Str::random( 6 ) ),
                    'referral_id' => null,
                    'referral_structure' => '-',
                ] );

                $createUserSocial = UserSocial::create( [
                    'platform' => request( 'platform' ),
                    'identifier' => request( 'identifier' ),
                    'uuid' => request( 'uuid' ),
                    'user_id' => $createUser->id,
                ] );
    
                return $createUserSocial;
    
            } catch ( \Throwable $th ) {
    
                \DB::rollBack();
                abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
            }
        } );

        \DB::commit();

        $user = User::find( $userSocial->user_id );

        // Register OneSignal
        if( !empty( $request->register_token ) ) {
            self::registerOneSignal( $user->id, $request->device_type, $request->register_token );
        }

        return response()->json( [ 'data' => $user, 'token' => $user->createToken( 'invictus_pro_app' )->plainTextToken ] );
    }

    public function getUser( $request ) {

        $userID = auth()->user()->id;
        if( !empty( $request->user_id ) ) {
            $userID = $request->user_id;
        }

        $user = User::find( $userID );

        return response()->json( [
            'message' => !$user ? __( 'api.user_not_found' ) : '',
            'data' => $user,
        ] );
    }

    public function updateUser( $request ) {

        $request->validate( [
            // 'country' => 'required|exists:countries,id',
            'username' => 'required|unique:users,username,' . auth()->user()->id,
            'email' => 'required|unique:users,email,' . auth()->user()->id . '|min:8',
            'phone_number' => [ 'required', function( $attributes, $value, $fail ) {
                // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
                $user = User::where( 'phone_number', $value )->first();
                if( $user ) {
                    if( $user->id != auth()->user()->id ) {
                        $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                    }
                }
            } ],
            'birthday' => 'required',
        ] );

        $updateUser = User::find( auth()->user()->id );
        // $updateUser->country_id = $request->country;
        $updateUser->username = $request->username;
        $updateUser->email = $request->email;
        $updateUser->phone_number = $request->phone_number;
        $updateUser->birthday = $request->birthday;

        if( $updateUser->isDirty() ) {
            $updateUser->save();    
        }

        return response()->json( [
            'message' => __( 'api.user_updated' ),
            'data' => $updateUser,
        ] );
    }

    public function updateUserPassword( $request ) {

        $request->validate( [
            'old_password' => [ 'required', 'min:8', function( $attributes, $value, $fail ) {
                if( !Hash::check( $value, auth()->user()->password ) ) {
                    $fail( __( 'api.old_password_not_match' ) );
                }
            } ],
            'password' => 'required|min:8|confirmed',
        ] );

        $updateUser = User::find( auth()->user()->id );
        $updateUser->password = Hash::make( $request->password );
        $updateUser->save();

        return response()->json( [
            'message' => __( 'api.user_password_updated' ),
            'data' => '',
        ] );
    }

    public function deleteUser( $request ) {

        $user = User::find( request()->user()->id );
        $user->delete();

        return response()->json( [
            'message' => __( 'api.user_deleted' ),
        ] );
    }

    public function forgotPassword( $request ) {

        $request->validate( [
            'email' => [ 'required', 'exists:users,email', function( $attributes, $value, $fail ) {
                if( User::find( auth()->user()->id )->is_social_account == 1 ) {
                    $fail( __( 'api.cannot_reset_social_account' ) );
                }
            } ]
        ] );
    }

    public function resetPassword( $request ) {

    }

    private function registerOneSignal( $user_id, $device_type, $register_token ) {
        
        UserDeviceOneSignal::updateOrCreate( 
            [ 'user_id' => $user_id, 'device_type' => 1 ],
            [ 'register_token' => $register_token ]
        );
    }
}