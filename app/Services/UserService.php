<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Crypt,
    DB,
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

    public static function allUsers( $request ) {

        $user = User::select( 'users.*' );

        $filterObject = self::filter( $request, $user );
        $user = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $user->orderBy( 'created_at', $dir );
                    break;
                case 2:
                    $user->orderBy( 'name', $dir );
                    break;
                case 3:
                    $user->orderBy( 'email', $dir );
                    break;
                case 4:
                    $user->orderBy( 'status', $dir );
                    break;
            }
        }

        $userCount = $user->count();

        $limit = $request->length;
        $offset = $request->start;

        $users = $user->skip( $offset )->take( $limit )->get();

        if ( $users ) {
            $users->append( [
                'encrypted_id',
            ] );
        }

        $totalRecord = User::count();

        $data = [
            'users' => $users,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $userCount : $totalRecord,
            'recordsTotal' => $totalRecord,
        ];

        return response()->json( $data );
    }

    private static function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->registered_date ) ) {
            if ( str_contains( $request->registered_date, 'to' ) ) {
                $dates = explode( ' to ', $request->registered_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'administrators.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->registered_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'administrators.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $request->username ) ) {
            $model->where( 'name', $request->username );
            $filter = true;
        }

        if ( !empty( $request->email ) ) {
            $model->where( 'email', $request->email );
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'status', $request->status );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneUser( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );
        
        $user = User::find( $request->id );

        return response()->json( $user );
    }

    public static function createUserAdmin( $request ) {

        $validator = Validator::make( $request->all(), [
            'username' => [ 'required', 'unique:users,username', 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:users,email', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'password' => [ 'required', Password::min( 8 ) ],
        ] );

        $attributeName = [
            'username' => __( 'user.username' ),
            'email' => __( 'user.email' ),
            'password' => __( 'user.password' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        DB::beginTransaction();

        try {

            $createUser = User::create( [
                'country_id' => 136,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make( $request->password ),
                'invitation_code' => strtoupper( \Str::random( 6 ) ),
                'referral_structure' => '-',
                'status' => 10,
            ] );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ] );
        }

        return response()->json( [
            'message' => __( 'template.new_x_created', [ 'title' => Str::singular( __( 'template.users' ) ) ] ),
        ] );
    }

    public static function updateUserAdmin( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'username' => [ 'required', 'unique:users,username,' . $request->id, 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:users,email,' . $request->id, 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'password' => [ 'nullable', Password::min( 8 ) ],
        ] );

        $attributeName = [
            'username' => __( 'user.username' ),
            'email' => __( 'user.email' ),
            'password' => __( 'user.password' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        DB::beginTransaction();

        try {

            $updateUser = User::find( $request->id );
            $updateUser->username = $request->username;
            $updateUser->email = $request->email;
            if ( !empty( $request->password ) ) {
                $updateUser->password = Hash::make( $request->password );
            }
            $updateUser->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ] );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.users' ) ) ] ),
        ] );
    }

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
                'message' => __( 'api.register_success' ),
                'message_key' => 'register_success',
                'data' => [],
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
            'message' => __( 'api.login_success' ),
            'message_key' => 'login_success',
            'data' => [
                'token' => $user->createToken( 'x_api' )->plainTextToken
            ],
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

        return response()->json( [ 'data' => $user, 'token' => $user->createToken( 'x_api' )->plainTextToken ] );
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