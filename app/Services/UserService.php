<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Crypt,
    Hash,
    Http,
    Validator,
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

use Illuminate\Validation\Rules\Password;

use App\Rules\CheckASCIICharacter;

use Helper;

class UserService {

    public static function requestOtp( $request ) {

        $validator = Validator::make( $request->all(), [
            'request_type' => [ 'required', 'in:1,2' ],
        ] );

        $attributeName = [
            'request_type' => __( 'user.request_type' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        if ( $request->request_type == 1 ) {

            $validator = Validator::make( $request->all(), [
                // 'phone_number' => [ 'required', 'integer', function( $attributes, $value, $fail ) {
                //     // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
                //     $user = User::where( 'phone_number', $value )->first();
                //     if ( $user ) {
                //         $fail( __( 'api.phone_number_is_taken' ) );
                //     }
                // } ],
                'email' => [ 'required', 'bail', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter, function( $attribute, $value, $fail ) {
                    $exist = User::where( 'email', $value )->where( 'status', 10 )->count();
                    if ( $exist > 0 ) {
                        $fail( __( 'validation.exists' ) );
                    }
                } ],
                'request_type' => [ 'required', 'in:1,2' ],
            ] );
    
            $attributeName = [
                'email' => __( 'user.email' ),
                'request_type' => __( 'user.request_type' ),
            ];
    
            foreach ( $attributeName as $key => $aName ) {
                $attributeName[$key] = strtolower( $aName );
            }
    
            $validator->setAttributeNames( $attributeName )->validate();
    
            $date = new \DateTime( date( 'Y-m-d H:i:s' ) );
            $date->add( new \DateInterval( 'PT15M' ) );
    
            \DB::beginTransaction();
    
            try {
                $createTmpUser = TmpUser::create( [
                    'country_id' => 136,
                    'email' => $request->email,
                    'otp_code' => mt_rand( 100000, 999999 ),
                    'status' => 1,
                    'expire_on' => $date->format( 'Y-m-d H:i:s' ),
                ] );
    
                \DB::commit();
    
                return response()->json( [
                    'message' => $createTmpUser->email,
                    'message_key' => 'request_otp_success',
                    'data' => [
                        'otp_code' => '#DEBUG - ' . $createTmpUser->otp_code,
                        'tmp_user' => Crypt::encryptString( $createTmpUser->id ),
                    ]
                ] );
    
            } catch ( \Throwable $th ) {
    
                \DB::rollBack();
                abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
            }
        } else {

            try {
                $request->merge( [
                    'tmp_user' => Crypt::decryptString( $request->tmp_user ),
                ] );
            } catch ( \Throwable $th ) {
                return response()->json( [
                    'message' => __( 'validation.header_message' ),
                    'errors' => [
                        'tmp_user' => [
                            __( 'api.otp_code_invalid' ),
                        ],
                    ]
                ], 422 );
            }

            $validator = Validator::make( $request->all(), [
                'tmp_user' => [ 'required', function( $attributes, $value, $fail ) {
    
                    $current = TmpUser::find( $value );
                    if ( !$current ) {
                        $fail( __( 'api.invalid_request' ) );
                        return false;
                    }
    
                    $exist = TmpUser::where( 'email', $current->email )->where( 'status', 1 )->count();
                    if ( $exist == 0 ) {
                        $fail( __( 'api.invalid_request' ) );
                        return false;
                    }
                } ],
            ] );

            $attributeName = [
                'tmp_user' => __( 'user.email' ),
            ];
    
            foreach ( $attributeName as $key => $aName ) {
                $attributeName[$key] = strtolower( $aName );
            }
    
            $validator->setAttributeNames( $attributeName )->validate();

            $date = new \DateTime( date( 'Y-m-d H:i:s' ) );
            $date->add( new \DateInterval( 'PT15M' ) );

            $updateTmpUser = TmpUser::find( $request->tmp_user );
            $updateTmpUser->otp_code = mt_rand( 100000, 999999 );
            $updateTmpUser->expire_on = $date->format( 'Y-m-d H:i:s' );
            $updateTmpUser->save();

            return response()->json( [
                'message' => $updateTmpUser->email,
                'message_key' => 'request_resend_otp_success',
                'data' => [
                    'otp_code' => '#DEBUG - ' . $updateTmpUser->otp_code,
                    'tmp_user' => Crypt::encryptString( $updateTmpUser->id ),
                ]
            ] );
        }
    }

    public static function verifyOTP( $request ) {

        $validator = Validator::make( $request->all(), [
            'request_type' => [ 'required', 'in:1' ],
        ] );

        $attributeName = [
            'request_type' => __( 'user.request_type' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        if ( $request->request_type == 1 ) {

            try {
                $request->merge( [
                    'tmp_user' => Crypt::decryptString( $request->tmp_user ),
                ] );
            } catch ( \Throwable $th ) {
                return response()->json( [
                    'message' => __( 'validation.header_message' ),
                    'errors' => [
                        'tmp_user' => [
                            __( 'api.otp_code_invalid' ),
                        ],
                    ]
                ], 422 );
            }

            $validator = Validator::make( $request->all(), [
                'otp_code' => 'required',
                'tmp_user' => [ 'required', function( $attributes, $value, $fail ) {
    
                    $current = TmpUser::find( $value );
                    if ( !$current ) {
                        $fail( __( 'api.otp_code_invalid' ) );
                        return false;
                    }
    
                    if ( $current->otp_code != request( 'otp_code' ) ) {
                        $fail( __( 'api.otp_code_invalid' ) );
                        return false;
                    }
    
                    $exist = TmpUser::where( 'email', $current->email )->where( 'status', 10 )->count();
                    if ( $exist > 1 ) {
                        $fail( __( 'validation.unique', strtolower( __( 'user.email' ) ) ) );
                        return false;
                    }
                } ],
            ] );

            $attributeName = [
                'otp_code' => __( 'user.otp_code' ),
                'tmp_user' => __( 'user.tmp_user' ),
            ];
    
            foreach ( $attributeName as $key => $aName ) {
                $attributeName[$key] = strtolower( $aName );
            }
    
            $validator->setAttributeNames( $attributeName )->validate();

        } else {

        }
    }

    public static function createUser( $request ) {

        try {
            $request->merge( [
                'tmp_user' => Crypt::decryptString( $request->tmp_user ),
            ] );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'message' => __( 'validation.header_message' ),
                'errors' => [
                    'tmp_user' => [
                        __( 'api.otp_code_invalid' ),
                    ],
                ]
            ], 422 );
        }

        $validator = Validator::make( $request->all(), [
            'otp_code' => 'required',
            'tmp_user' => [ 'required', function( $attributes, $value, $fail ) {

                $current = TmpUser::find( $value );
                if ( !$current ) {
                    $fail( __( 'api.otp_code_invalid' ) );
                    return false;
                }

                if ( $current->otp_code != request( 'otp_code' ) ) {
                    $fail( __( 'api.otp_code_invalid' ) );
                    return false;
                }

                $exist = TmpUser::where( 'email', $current->email )->where( 'status', 10 )->count();
                if ( $exist > 1 ) {
                    $fail( __( 'validation.unique', strtolower( __( 'user.email' ) ) ) );
                    return false;
                }
            } ],
            'username' => [ 'required', 'alpha_dash', 'unique:users,username' ],
            'email' => [ 'required', 'unique:users,email', 'min:8', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'password' => [ 'required', Password::min( 8 ) ],
            // 'country' => [ 'required', 'exists:countries,id' ],
            // 'phone_number' => [ 'required', 'integer', function( $attributes, $value, $fail ) {
            //     // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
            //     $user = User::where( 'phone_number', $value )->first();
            //     if ( $user ) {
            //         $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
            //     }
            // } ],
            'invitation_code' => [ 'sometimes', 'exists:users,invitation_code' ],
            'device_type' => [ 'required', 'in:1,2,3' ],
        ] );

        $attributeName = [
            'email' => __( 'user.email' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        \DB::beginTransaction();

        try {

            $createUserObject = [
                'username' => $request->username,
                'email' => $request->email,
                'country_id' => 136,
                // 'phone_number' => $request->phone_number,
                'password' => Hash::make( $request->password ),
                'invitation_code' => strtoupper( \Str::random( 6 ) ),
            ];

            $referral = User::where( 'invitation_code', $request->invitation_code )->first();

            if ( $referral ) {
                $createUserObject['referral_id'] = $referral->id;
                $createUserObject['referral_structure'] = $referral->referral_structure . '|' . $referral->id;
            } else {
                $createUserObject['referral'] = 0;
                $createUserObject['referral_structure'] = '-';
            }

            $createUser = User::create( $createUserObject );

            $updateTmpUser = TmpUser::find( $request->tmp_user );
            $updateTmpUser->status = 10;
            $updateTmpUser->save();

            // Register OneSignal
            if ( !empty( $request->register_token ) ) {
                self::registerOneSignal( $createUser->id, $request->device_type, $request->register_token );
            }

            \DB::commit();

            return response()->json( [
                'data' => [],
                'message' => __( 'api.register_success' ),
                'message_key' => 'register_success',
            ] );

        } catch ( \Throwable $th ) {

            \DB::rollBack();
            abort( 500, $th->getMessage() . ' in line: ' . $th->getLine() );
        }
    }

    public static function createToken( $request ) {

        $request->merge( [ 'account' => 'test' ] );

        $request->validate( [
            'username' => 'required',
            'password' => 'required',
            'account' => [ 'sometimes', function( $attributes, $value, $fail ) {

                $user = User::where( 'username', request( 'username' ) )->first();
                if ( !$user ) {
                    $fail( __( 'api.user_wrong_user_password' ) );
                    return 0;
                }

                if ( !Hash::check( request( 'password' ), $user->password ) ) {
                    $fail( __( 'api.user_wrong_user_password' ) );
                    return 0;
                }
            } ],
            'device_type' => 'required|in:1,2,3',
        ] );

        $user = User::where( 'username', $request->username )->first();

        // Register OneSignal
        if ( !empty( $request->register_token ) ) {
            self::registerOneSignal( $user->id, $request->device_type, $request->register_token );
        }

        return response()->json( [
            'data' => [
                'token' => $user->createToken( 'birdnest_api' )->plainTextToken
            ],
            'message' => __( 'api.login_success' ),
            'message_key' => 'login_success',
        ] );
    }

    public static function createTokenSocial( $request ) {

        $request->validate( [
            'identifier' => [ 'required', function( $attributes, $value, $fail ) {
                $user = User::where( 'email', $value )->where( 'is_social_account', 0 )->first();
                if ( $user ) {
                    $fail( __( 'api.email_is_taken_not_social' ) );
                }
                $userSocial = UserSocial::where( 'identifier', $value )->first();
                if ( $userSocial ) {
                    if ( $userSocial->platform != request('platform') ) {
                        $fail( __( 'api.email_is_taken_different_platform' ) );
                    }
                }
            } ],
            'platform' => 'required|in:1,2',
            'device_type' => 'required|in:1,2,3',
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
        if ( !empty( $request->register_token ) ) {
            self::registerOneSignal( $user->id, $request->device_type, $request->register_token );
        }

        return response()->json( [ 'data' => $user, 'token' => $user->createToken( 'birdnest_api' )->plainTextToken ] );
    }

    public static function getUser( $request ) {

        $userID = auth()->user()->id;

        $user = User::find( $userID );

        if ( $user ) {
            $user->makeHidden( [
                'name',
                'email_verified_at',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'is_social_account',
                'birthday',
                'referral_id',
                'referral_structure',
                'status',
                'updated_at',
            ] );
        }

        return response()->json( [
            'message' => !$user ? __( 'api.user_not_found' ) : '',
            'message_key' => !$user ? 'get_user_failed' : 'get_user_success',
            'data' => $user,
        ] );
    }

    public static function updateUser( $request ) {

        $request->validate( [
            // 'country' => 'required|exists:countries,id',
            'username' => 'required|unique:users,username,' . auth()->user()->id,
            'email' => 'required|unique:users,email,' . auth()->user()->id . '|min:8',
            'phone_number' => [ 'required', function( $attributes, $value, $fail ) {
                // $user = User::where( 'country_id', request( 'country' ) )->where( 'phone_number', $value )->first();
                $user = User::where( 'phone_number', $value )->first();
                if ( $user ) {
                    if ( $user->id != auth()->user()->id ) {
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

        if ( $updateUser->isDirty() ) {
            $updateUser->save();    
        }

        return response()->json( [
            'message' => __( 'api.user_updated' ),
            'data' => $updateUser,
        ] );
    }

    public static function updateUserPassword( $request ) {

        $request->validate( [
            'old_password' => [ 'required', 'min:8', function( $attributes, $value, $fail ) {
                if ( !Hash::check( $value, auth()->user()->password ) ) {
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

    public static function deleteUser( $request ) {

        $user = User::find( request()->user()->id );
        $user->delete();

        return response()->json( [
            'message' => __( 'api.user_deleted' ),
        ] );
    }

    public static function forgotPassword( $request ) {

        $request->validate( [
            'email' => [ 'required', 'exists:users,email', function( $attributes, $value, $fail ) {
                if ( User::find( auth()->user()->id )->is_social_account == 1 ) {
                    $fail( __( 'api.cannot_reset_social_account' ) );
                }
            } ]
        ] );
    }

    public static function resetPassword( $request ) {

    }

    private function registerOneSignal( $user_id, $device_type, $register_token ) {
        
        UserDeviceOneSignal::updateOrCreate( 
            [ 'user_id' => $user_id, 'device_type' => 1 ],
            [ 'register_token' => $register_token ]
        );
    }
}