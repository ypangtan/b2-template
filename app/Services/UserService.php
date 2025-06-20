<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Hash,
    Http,
    Storage,
    Validator,
};
use App\Models\{
    ApiLog,
    Country,
    OtpAction,
    TmpUser,
    User,
    UserDetail,
    UserDevice,
    UserSocial,
    UserStructure,
    UserWallet,
};

use Illuminate\Validation\Rules\Password;

use App\Rules\CheckASCIICharacter;

use Helper;

use Carbon\Carbon;

class UserService {

    public static function allUsers( $request ) {

        $user = User::with( [
            'country',
            'referral',
            'referral.userDetail',
            'userDetail',
        ] )->select( 'users.*' );

        $filterObject = self::filter( $request, $user );
        $user = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $user->orderBy( 'created_at', $dir );
                    break;
            }
        }

        $userCount = $user->count();

        $limit = $request->length;
        $offset = $request->start;

        $users = $user->skip( $offset )->take( $limit )->get();

        $users->append( [
            'encrypted_id',
        ] );

        $users->each( function( $u ) {
            if ( $u->userDetail ) {
                $u->userDetail->append( [
                    'photo_path',
                ] );
            }
        } );

        $user = User::select(
            DB::raw( 'COUNT(users.id) as total'
        ) );

        $filterObject = self::filter( $request, $user );
        $user = $filterObject['model'];
        $filter = $filterObject['filter'];

        $user = $user->first();

        $data = [
            'users' => $users,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $userCount : $user->total,
            'recordsTotal' => $filter ? User::count() : $userCount,
        ];

        return $data;
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

                $model->whereBetween( 'users.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->registered_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'users.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if ( !empty( $request->user ) ) {
            $model->where( function( $query ) use ( $request ) {
                $query->where( 'users.email', 'LIKE', '%' . $request->user . '%' );
                $query->orWhere( 'users.username', 'LIKE', '%' . $request->user , '%' );
                // $query->orWhereHas( 'userDetail', function( $query ) use ( $request ) {
                //     $query->where( 'user_details.fullname', 'LIKE', '%' . $request->user . '%' );
                // } );
            } );
            $filter = true;
        }

        if ( !empty( $request->phone_number ) ) {
            $model->where( function( $query ) use ( $request ) {
                $query->where( 'users.phone_number', $request->phone_number );
                $query->orWhere( DB::raw( "CONCAT( calling_code, phone_number )" ), 'LIKE', '%' . $request->phone_number );
            } );
            $filter = true;
        }

        if ( !empty( $request->referral ) ) {
            $model->whereHas( 'referral', function( $query ) use ( $request ) {
                $query->where( 'users.email', $request->referral );
                $query->orWhereHas( 'userDetail', function( $query ) use ( $request ) {
                    $query->where( 'user_details.fullname', 'LIKE', '%' . $request->referral . '%' );
                } );
            } );
            $filter = true;
        }

        if ( !empty( $request->role ) ) {
            $model->where( 'users.role', $request->role );
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'users.status', $request->status );
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
        
        $user = User::with( [
            'userDetail'
        ] )->find( $request->id );

        return $user;
    }

    public static function createUserAdmin( $request ) {

        DB::beginTransaction();

        if( !empty( $request->referral ) ) { 
            $request->merge( [
                'referral' => \Helper::decode( $request->referral )
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'username' => [ 'nullable', 'unique:users,username', 'alpha_num', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:users,email', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'calling_code' => [ 'nullable' ],
            'phone_number' => [ 'nullable', 'digits_between:8,15', function( $attribute, $value, $fail ) use ( $request ) {
                
                $exist = User::where( 'calling_code', $request->calling_code )
                    ->where( 'phone_number', $value )
                    ->first();

                if ( $exist ) {
                    $fail( __( 'validation.exists' ) );
                    return false;
                }
            } ],
            'referral' => [ 'nullable', 'exists:users,id' ],
            'security_pin' => [ 'nullable', 'numeric', 'digits:6' ],
            'password' => [ 'required', Password::min( 8 ) ],
        ] );

        $attributeName = [
            'username' => __( 'user.username' ),
            'email' => __( 'user.email' ),
            'calling_code' => __( 'user.calling_code' ),
            'phone_number' => __( 'user.phone_number' ),
            'referral' => __( 'user.referral' ),
            'security_pin' => __( 'user.security_pin' ),
            'password' => __( 'user.password' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $createUserObject['user'] = [
                'country_id' => 136,
                'username' => $request->username,
                'email' => $request->email,
                'calling_code' => $request->calling_code,
                'phone_number' => $request->phone_number,
                'password' => Hash::make( $request->password ),
                'security_pin' => Hash::make( $request->security_pin ),
                'invitation_code' => strtoupper( Str::random( 6 ) ),
                'status' => 10,
            ];

            $createUserObject['user_detail'] = [
                'fullname' => $request->username,
            ];

            if( !empty( $request->refferal ) ) {
                $referral = User::find( $request->referral );
            }

            if ( isset( $referral ) ) {
                $createUserObject['user']['referral_id'] = $referral->id;
                $createUserObject['user']['referral_structure'] = $referral->referral_structure . '|' . $referral->id;
            } else {
                $createUserObject['user']['referral_id'] = null;
                $createUserObject['user']['referral_structure'] = '-';
            }

            $createUser = self::create( $createUserObject );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.new_x_created', [ 'title' => Str::singular( __( 'template.users' ) ) ] ),
        ] );
    }

    public static function updateUserAdmin( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        if( !empty( $request->referral ) ) { 
            $request->merge( [
                'referral' => \Helper::decode( $request->referral )
            ] );
        }

        $validator = Validator::make( $request->all(), [
            'username' => [ 'nullable', 'unique:users,username,' . $request->id, 'alpha_num', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:users,email,' . $request->id, 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'calling_code' => [ 'nullable' ],
            'phone_number' => [ 'nullable', 'digits_between:8,15', function( $attribute, $value, $fail ) use ( $request ) {
                
                $exist = User::where( 'calling_code', $request->calling_code )
                    ->where( 'phone_number', $value )
                    ->where( 'id', '!=', $request->id )
                    ->first();

                if ( $exist ) {
                    $fail( __( 'validation.exists' ) );
                    return false;
                }
            } ],
            'referral' => [ 'nullable', 'exists:users,id' ],
            'security_pin' => [ 'nullable', 'numeric', 'digits:6' ],
            'password' => [ 'nullable', Password::min( 8 ) ],
        ] );

        $attributeName = [
            'username' => __( 'user.username' ),
            'email' => __( 'user.email' ),
            'calling_code' => __( 'user.calling_code' ),
            'phone_number' => __( 'user.phone_number' ),
            'referral' => __( 'user.referral' ),
            'security_pin' => __( 'user.security_pin' ),
            'password' => __( 'user.password' ),
        ];

        foreach( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();    

        try {

            $updateUser = User::with( [
                'userDetail',
            ] )->lockForUpdate()
                ->find( $request->id );

            $updateUser->username = $request->username;
            $updateUser->email = $request->email;
            $updateUser->calling_code = $request->calling_code;
            $updateUser->phone_number = $request->phone_number;
            
            if ( !empty( $request->password ) ) {
                $updateUser->password = Hash::make( $request->password );
            }
            if ( !empty( $request->security_pin ) ) {
                $updateUser->security_pin = Hash::make( $request->security_pin );
            }
            
            if( !empty( $request->referral ) ) {
                $referral = User::find( $request->referral );
            }

            if ( isset( $referral ) ) {
                if( $updateUser->referral_id != $referral->id ){
                    $updated_referral_structure = $referral->referral_structure . '|' . $referral->id;
                    $before_referral_structure = $updateUser->referral_structure . '|' . $updateUser->id;

                    $downlines = User::where( 'referral_structure', 'like', $before_referral_structure . '%' )->get();
                    foreach( $downlines as $downline ){
                        
                        $updateUserStructures = UserStructure::where( 'user_id', $downline->id )
                            ->get();
                        foreach( $updateUserStructures as $updateUserStructure ){
                            $updateUserStructure->delete();
                        }

                        $downline->referral_structure = str_replace( $before_referral_structure, $updated_referral_structure . '|' . $updateUser->id, $downline->referral_structure );
                        $downline->save();

                        $referralArray = explode( '|', $downline->referral_structure );
                        $referralLevel = count( $referralArray );
                        for ( $i = $referralLevel - 1; $i >= 0; $i-- ) {
                            if ( $referralArray[$i] != '-' ) {
                                UserStructure::create( [
                                    'user_id' => $downline->id,
                                    'referral_id' => $referralArray[$i],
                                    'level' => $referralLevel - $i
                                ] );
                            }
                        }
                    }

                    $updateUser->referral_id = $referral->id;
                    $updateUser->referral_structure = $updated_referral_structure;
                    
                    $updateUserStructures = UserStructure::where( 'user_id', $updateUser->id )
                        ->get();
                        
                    foreach( $updateUserStructures as $updateUserStructure ){
                        $updateUserStructure->delete();
                    }
                    
                    $referralArray = explode( '|', $updateUser->referral_structure );
                    $referralLevel = count( $referralArray );
                    for ( $i = $referralLevel - 1; $i >= 0; $i-- ) {
                        if ( $referralArray[$i] != '-' ) {
                            UserStructure::create( [
                                'user_id' => $updateUser->id,
                                'referral_id' => $referralArray[$i],
                                'level' => $referralLevel - $i
                            ] );
                        }
                    }
                }
            } else {
                $updated_referral_structure = '-';
                $before_referral_structure = $updateUser->referral_structure . '|' . $updateUser->id;

                $downlines = User::where( 'referral_structure', 'like', $before_referral_structure . '%' )->get();
                foreach( $downlines as $downline ){
                    
                    $updateUserStructures = UserStructure::where( 'user_id', $downline->id )
                        ->get();
                    foreach( $updateUserStructures as $updateUserStructure ){
                        $updateUserStructure->delete();
                    }

                    $downline->referral_structure = str_replace( $before_referral_structure, $updated_referral_structure . '|' . $updateUser->id, $downline->referral_structure );
                    $downline->save();

                    $referralArray = explode( '|', $downline->referral_structure );
                    $referralLevel = count( $referralArray );
                    for ( $i = $referralLevel - 1; $i >= 0; $i-- ) {
                        if ( $referralArray[$i] != '-' ) {
                            UserStructure::create( [
                                'user_id' => $downline->id,
                                'referral_id' => $referralArray[$i],
                                'level' => $referralLevel - $i
                            ] );
                        }
                    }
                }
                
                $updateUser->referral_id = null;
                $updateUser->referral_structure = $updated_referral_structure;
                
                $updateUserStructures = UserStructure::where( 'user_id', $updateUser->id )
                    ->get();
                    
                foreach( $updateUserStructures as $updateUserStructure ){
                    $updateUserStructure->delete();
                }
            }
            $updateUser->save();

            $updateUserDetail = UserDetail::where( 'user_id', $request->id )
                ->lockForUpdate()
                ->first();
            $updateUserDetail->fullname = $request->username;
            $updateUserDetail->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.users' ) ) ] ),
        ] );
    }

    public static function updateUserStatus( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'status' => 'required',
        ] );
        
        $validator->validate();

        try {

            $updateUser = User::lockForUpdate()->find( $request->id );
            $updateUser->status = $request->status;
            $updateUser->save();

            DB::commit();
            
            return response()->json( [
                'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.users' ) ) ] ),
            ] );

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }
    }

    public static function getTeamAjax( $request ) {

        if( !empty( $request->email ) ){
            $request->merge( [
                'email' => Helper::decode( $request->email )
            ] );
        }

        $searcher = [];

        if ( $request->id == '#' ) {
            if ( $request->email ) {
                $username = $request->email;
            } else {
                $username = User::first()->value( 'id' );
            }
        } else {
            $username = $request->email == 0 ? User::first()->value( 'id' ) : $request->email;
            $searcher = User::find( $request->id );
        }

        $user = User::where( 'id', $username )->first();

        if ( !$user ) {
            return [];
        }

        if ( !$searcher ) {
            $searcher = $user;
        }

        $downlines = UserStructure::with( [
            'user',
            'user.downlines',
            'user.overriding',
        ] )->where( 'referral_id', $searcher ? $searcher->id : $user->id )
            ->where('level', 1)->get();

        $data = [];

        foreach ( $downlines as $downline ) {

            $downline->user->append( [ 'total_group_sale', 'direct_sponsors', 'ranking_path' ] );

            if ( count( $downline->user->groups->pluck( 'user_id' ) ) == 0 ) {
                $groupMember = 0;
            } else {
                $groupMember = count( $downline->user->groups->pluck( 'user_id' ) );
            }

            $ranking_path = $downline->user->ranking_path;
            $html = '';

            $html .= '
            <div class="flex flex-col gap-y-4 rounded-[10px] border-[2px] border-l-[5px] border-[#ECECEC] !border-l-[#FFCA05] sm:w-[180px] md:w-[800px] my-2 px-3 py-3 relative">
                <div class="flex justify-between items-center gap-x-2 ">
                    <div class="flex gap-x-8 items-center">
                        <div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[14px] md:text-[16px] font-bold text-[#212121] mb-1">' . ( $downline->user->userDetail != null ? $downline->user->userDetail->fullname : $downline->user->email ) . '</h4>
                            </div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[10px] md:text-[14px] font-bold text-[#212121] mb-1">' . $downline->user->email . '</h4>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-center items-center">
                                ' .  ( $ranking_path == null ? '<h4 class="ranking"> - </h4>' : ( '<img src="' . $ranking_path . '" class="ranking_img" />' ) ) . '
                            </div>
                        </div>
                    </div>'.
                    ( $groupMember >= 1 ? '<i class="bi bi-caret-down-fill"></i>' : '' ).'
                </div>
                <div class="flex justify-around items-center">
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $downline->user->direct_sponsors[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.direct_sponsors' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $downline->user->direct_sponsors[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1"> '. __( 'user.direct_sponsors_sales' ) .' </h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $downline->user->total_group_sale[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_members' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $downline->user->total_group_sale[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_team_sales' ) .'</h4>
                    </div>
                </div>
            </div>
            ';

            $data[] = [
                'id' => $downline->user->id,
                'name' => $downline->user->id,
                'text' => $html,
                'children' => count( $downline->user->downlines ) > 0,
            ];
        }
        
        return $data;
    }

    public static function getTeamData( $request ) {

        if( !empty( $request->email ) ){
            $request->merge( [
                'email' => Helper::decode( $request->email )
            ] );
        }

        $users = User::with( [
            'overriding',
            'userDetail',
        ] );

        if ( $request->id !== '0' ) {
            $users = $users->where( 'id', Helper::decode( $request->id ) );
        } else {
            $users = $users->where( 'referral_id', null )
            ->orderBy( 'id', 'DESC' );
        }

        $users = $users->get();

        $users->append( [ 'total_group_sale', 'direct_sponsors', 'ranking_path' ] );

        $html = '';


        foreach ( $users as $user ) {

            if ( count( $user->groups->pluck( 'user_id' ) ) == 0 ) {
                $groupMember = 0;
            } else {
                $groupMember = count( $user->groups->pluck( 'user_id' ) );
            }

            $ranking_path = $user->ranking_path;

            $html .= '
            <div class="flex flex-col gap-y-4 rounded-[10px] border-[2px] border-l-[5px] border-[#ECECEC] !border-l-[#FFCA05] sm:w-[180px] md:w-[800px] my-2 px-3 py-3 relative">
                <div class="flex justify-between items-center gap-x-2 ">
                    <div class="flex gap-x-8 items-center">
                        <div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[14px] md:text-[16px] font-bold text-[#212121] mb-1">' . ( $user->userDetail != null ? $user->userDetail->fullname : $user->email ) . '</h4>
                            </div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[10px] md:text-[14px] font-bold text-[#212121] mb-1">' . $user->email . '</h4>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-center items-center">
                                ' .  ( $ranking_path == null ? '<h4 class="ranking"> - </h4>' : ( '<img src="' . $ranking_path . '" class="ranking_img" />' ) ) . '
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-around items-center">
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $user->direct_sponsors[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.direct_sponsors' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $user->direct_sponsors[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1"> '. __( 'user.direct_sponsors_sales' ) .' </h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $user->total_group_sale[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_members' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $user->total_group_sale[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_team_sales' ) .'</h4>
                    </div>
                </div>
            </div>
            ';
        }

        return [
            'html' => $html,
        ];
    }

    public static function getUplineData( $request ) {

        if( !empty( $request->email ) ){
            $request->merge( [
                'email' => Helper::decode( $request->email )
            ] );
        }

        $users = User::with( [
            'upline',
            'upline.overriding',
            'upline.userDetail',
        ] );

        if ( $request->id !== '0' ) {
            $users = $users->where( 'id', Helper::decode( $request->id ) );
        }

        $users = $users->first();

        $html = '';
        if( !$users->upline ){
            return [
                'html' => $html,
            ];
        }
        $users = $users->upline;

        $users->append( [ 'total_group_sale', 'direct_sponsors', 'ranking_path' ] );

        $ranking_path = $users->ranking_path;
        $html .= '
            <div class="flex flex-col gap-y-4 rounded-[10px] border-[2px] border-l-[5px] border-[#ECECEC] !border-l-[#FFCA05] sm:w-[180px] md:w-[800px] my-2 px-3 py-3 relative">
                <div class="flex justify-between items-center gap-x-2 ">
                    <div class="flex gap-x-8 items-center">
                        <div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[14px] md:text-[16px] font-bold text-[#212121] mb-1">' . ( $users->userDetail != null ? $users->userDetail->fullname : $users->email ) . '</h4>
                            </div>
                            <div class="flex gap-x-2">
                                <h4 class="text-[10px] md:text-[14px] font-bold text-[#212121] mb-1">' . $users->email . '</h4>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-center items-center">
                                ' .  ( $ranking_path == null ? '<h4 class="ranking"> - </h4>' : ( '<img src="' . $ranking_path . '" class="ranking_img" />' ) ) . '
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-around items-center">
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $users->direct_sponsors[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.direct_sponsors' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $users->direct_sponsors[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1"> '. __( 'user.direct_sponsors_sales' ) .' </h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $users->total_group_sale[ 'downlineCount' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_members' ) .'</h4>
                    </div>
                    <div class="flex flex-col justify-center items-center">
                        <h4 class="text-[10px] md:text-[14px] font-bold text-[#977200] mb-1">' . $users->total_group_sale[ 'total_sale' ] . '</h4>
                        <h4 class="text-[10px] md:text-[14px] text-[#8D8D8D] mb-1">'. __( 'user.total_team_sales' ) .'</h4>
                    </div>
                </div>
            </div>
            ';

        return [
            'html' => $html,
        ];
    }

    public static function getTeamLeader( $request ) {
        $user = UserStructure::where( 'user_id', \Helper::decode( $request->id ) )
            ->orderBy( 'level', 'desc' )
            ->first();

        $leader = '';

        if( $user ) {
            $leader = User::with( [
                'overriding',
            ] )->find( $user->referral_id );

            if( $leader ){
                $leader->append( 'total_group_sale' );
            }
        }

        return [
            'data' => $leader
        ];
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
                'calling_code' => [ 'nullable' ],
                'phone_number' => [ 'nullable', 'digits_between:8,15', function( $attributes, $value, $fail ) use ( $request ) {
                    $user = User::where( 'phone_number', $value )->where( 'calling_code', $request->calling_code )->first();
                    if ( $user ) {
                        $fail( __( 'api.phone_number_is_taken' ) );
                    }
                } ],
                'email' => [ 'required', 'bail', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter, 'unique:users,email' ],
            ] );
    
            $attributeName = [
                'calling_code' => __( 'user.calling_code' ),
                'phone_number' => __( 'user.phone_number' ),
                'email' => __( 'user.email' ),
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
                    'phone_number' => isset( $request->phone_number ) ? $request->phone_number : null,
                    'calling_code' => isset( $request->calling_code ) ? $request->calling_code : null,
                    'email' => isset( $request->email ) ? $request->email : null,
                    'otp_code' => mt_rand( 100000, 999999 ),
                    'status' => 1,
                    'expire_on' => $date->format( 'Y-m-d H:i:s' ),
                ] );
    
                \DB::commit();
    
                return response()->json( [
                    'message' => __( 'api.request_otp_success' ),
                    'message_key' => 'request_otp_success',
                    'data' => [
                        'otp_code' => '#DEBUG - ' . $createTmpUser->otp_code,
                        'tmp_user' => Crypt::encryptString( $createTmpUser->id ),
                    ]
                ] );
    
            } catch ( \Throwable $th ) {
    
                \DB::rollBack();
                return response()->json( [
                    'message' => $th->getMessage() . ' in line: ' . $th->getLine()
                ], 500 );
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
                'message' => __( 'api.request_resend_otp_success' ),
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

            $current = TmpUser::find( $request->tmp_user );
            $current->status = 2;
            $current->save();
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
            } ],
            'username' => [ 'required', 'alpha_dash', 'unique:users,username', new CheckASCIICharacter ],
            'email' => [ 'required', 'unique:users,email', 'min:8', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'password' => [ 'required', Password::min( 8 ) ],
            'security_pin' => [ 'required', 'numeric' ],
            'calling_code' => [ 'nullable' ],
            'phone_number' => [ 'nullable', 'digits_between:8,15', function( $attributes, $value, $fail ) use ( $request ) {
                $user = User::where( 'phone_number', $value )->where( 'calling_code', $request->calling_code )->first();
                if ( $user ) {
                    $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                }
            } ],
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
            $createUserObject['user'] = [
                'username' => $request->username,
                'email' => $request->email,
                'country_id' => 136,
                'calling_code' => $request->calling_code,
                'phone_number' => $request->phone_number,
                'password' => Hash::make( $request->password ),
                'invitation_code' => strtoupper( \Str::random( 6 ) ),
            ];

            $createUserObject['user_detail'] = [
                'fullname' => $request->username,
            ];

            if( !empty( $request->invitation_code ) ) {
                $referral = User::where( 'invitation_code', $request->invitation_code )->first();
            }

            if ( isset( $referral ) ) {
                $createUserObject['user']['referral_id'] = $referral->id;
                $createUserObject['user']['referral_structure'] = $referral->referral_structure . '|' . $referral->id;
            } else {
                $createUserObject['user']['referral_id'] = null;
                $createUserObject['user']['referral_structure'] = '-';
            }

            $createUser = self::create( $createUserObject );

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

                $user = User::where( 'email', request( 'email' ) )->first();
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

        $user = User::where( 'email', $request->email )->first();

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

        $user = User::with( [
            'country',
            'userDetail',
        ] )->find( $userID );

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
            
            if( $user->userDetail ) {
                $user->userDetail->append( 'photo_path' );
            }
        }

        return response()->json( [
            'message' => !$user ? __( 'api.user_not_found' ) : '',
            'message_key' => !$user ? 'get_user_failed' : 'get_user_success',
            'data' => $user,
        ] );
    }

    public static function updateUser( $request ) {

        $request->validate( [
            'username' => 'required|unique:users,username,' . auth()->user()->id,
            'email' => 'nullable|unique:users,email,' . auth()->user()->id . '|min:8',
            'calling_code' => [ 'nullable' ],
            'phone_number' => [ 'nullable', function( $attributes, $value, $fail ) use ( $request ) {
                $user = User::where( 'phone_number', $value )->where( 'calling_code', $request->calling_code )->first();
                if ( $user ) {
                    if ( $user->id != auth()->user()->id ) {
                        $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                    }
                }
            } ],
        ] );

        $updateUser = User::find( auth()->user()->id );
        $updateUser->username = $request->username;
        if( isset( $request->email ) ) {
            $updateUser->email = $request->email;
        }
        if( isset( $request->calling_code ) ) {
            $updateUser->calling_code = $request->calling_code;
            $updateUser->phone_number = $request->phone_number;
        }

        if ( $updateUser->isDirty() ) {
            $updateUser->save();    
        }

        return response()->json( [
            'message' => __( 'api.user_updated' ),
            'data' => $updateUser,
        ] );
    }

    public static function updateUserPhoto( $request ) {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'photo' => [ 'required', 'file', 'mimes:jpg,png' ],
        ]);    
    
        $attributeNames = [
            'photo' => __('user.photo'),
        ];
    
        $validator->setAttributeNames($attributeNames)->validate();
        try {

            if( $request->hasFile( 'photo' ) ){
                $updateUser = UserDetail::where( 'user_id', auth()->user()->id )->first();
                if( $updateUser ){
                    if( $updateUser->photo ){
                        Storage::disk('public')->delete($updateUser->photo);
                    }
                    $updateUser->photo = $request->file('photo')->store('users', ['disk' => 'public']);
                    if( $updateUser->save() ){
                        $updateUser->append( 'photo_path' );
                    }
                }
                
                DB::commit();
                
                return response()->json( [
                    'message_key' => __( 'api.user_photo_updated' ),
                    'message' => __( 'api.user_photo_updated' ),
                    'data' => $updateUser,
                ] );
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message_key' => __( 'api.user_photo_updated_fail' ),
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500);
        }
    }

    public static function updateUserPassword( $request ) {

        $validator = Validator::make( $request->all(), [
            'old_password' => [ 'required', 'min:8', function( $attributes, $value, $fail ) {
                if ( !Hash::check( $value, auth()->user()->password ) ) {
                    $fail( __( 'api.old_password_not_match' ) );
                }
            } ],
            'password' => [ 'required', Password::min( 8 ), 'confirmed' ],
        ] );

        $attributeName = [
            'password' => __( 'user.password' ),
            'confirm_password' => __( 'user.confirm_password' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        $updateUser = User::find( auth()->user()->id );
        $updateUser->password = Hash::make( $request->password );
        $updateUser->save();

        return response()->json( [
            'message_key' => __( 'api.user_password_updated' ),
            'message' => __( 'api.user_password_updated' ),
            'data' => '',
        ] );
    }

    public static function updateSecurityPin( $request ) {

        if( auth()->user()->security_pin != null ){
            $validator = Validator::make( $request->all(), [
                'old_security_pin' => [ 'required', function( $attributes, $value, $fail ) {
                    if ( !Hash::check( $value, auth()->user()->security_pin ) ) {
                        $fail( __( 'api.old_security_pin_not_match' ) );
                    }
                } ],
                'security_pin' => [ 'required', 'confirmed', 'digits:6', 'numeric' ],
            ] );
        }else{
            $validator = Validator::make( $request->all(), [
                'security_pin' => [ 'required', 'confirmed', 'digits:6', 'numeric' ],
            ] );
        }

        $attributeName = [
            'old_security_pin' => __( 'user.old_security_pin' ),
            'security_pin' => __( 'user.security_pin' ),
            'confirm_security_pin' => __( 'user.confirm_security_pin' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        $updateUser = User::find( auth()->user()->id );
        $updateUser->security_pin = Hash::make( $request->security_pin );
        $updateUser->save();

        return response()->json( [
            'message_key' => __( 'api.user_security_pin_updated' ),
            'message' => __( 'api.user_security_pin_updated' ),
            'data' => '',
        ] );
    }

    public static function forgotPassword( $request ) {

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
                'email' => [ 'required', 'unique:users,email', 'min:8', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
                'calling_code' => [ 'nullable' ],
                'phone_number' => [ 'nullable', 'integer', function( $attributes, $value, $fail ) {
                    $user = User::where( 'calling_code', request( 'calling_code' ) )->where( 'phone_number', $value )->first();
                    if ( $user ) {
                        $fail( __( 'validation.unique', [ 'attribute' => 'phone number' ] ) );
                    }
                } ],
            ] );
    
            $attributeName = [
                'email' => __( 'user.email' ),
                'calling_code' => __( 'user.calling_code' ),
                'phone_number' => __( 'user.phone_number' ),
            ];
    
            foreach ( $attributeName as $key => $aName ) {
                $attributeName[$key] = strtolower( $aName );
            }
    
            $validator->setAttributeNames( $attributeName )->validate();
    
            $date = new \DateTime( date( 'Y-m-d H:i:s' ) );
            $date->add( new \DateInterval( 'PT10M' ) );
    
            \DB::beginTransaction();
    
            try {
                $createTmpUser = OtpAction::create( [
                    'email' => $request->email,
                    'calling_code' => $request->calling_code,
                    'phone_number' => $request->phone_number,
                    'otp_code' => mt_rand( 100000, 999999 ),
                    'status' => 1,
                    'expire_on' => $date->format( 'Y-m-d H:i:s' ),
                ] );
                    
                // TODO: send mail

                \DB::commit();

                return response()->json( [
                    'message' => $createTmpUser->email,
                    'message_key' => __( 'api.request_otp_success' ),
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
        else if ( $request->request_type == 2 ) {

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
    
                    $current = OtpAction::find( $value );
                    if ( !$current ) {
                        $fail( __( 'api.invalid_request' ) );
                        return false;
                    }
                } ],
            ] );

            $attributeName = [
                'tmp_user' => __( 'user.otp_code' ),
            ];
    
            foreach ( $attributeName as $key => $aName ) {
                $attributeName[$key] = strtolower( $aName );
            }
    
            $validator->setAttributeNames( $attributeName )->validate();

            $date = new \DateTime( date( 'Y-m-d H:i:s' ) );
            $date->add( new \DateInterval( 'PT10M' ) );

            $updateTmpUser = OtpAction::find( $request->tmp_user );
            $updateTmpUser->otp_code = mt_rand( 100000, 999999 );
            $updateTmpUser->expire_on = $date->format( 'Y-m-d H:i:s' );
            $updateTmpUser->save();

            // TODO: send mail

            return response()->json( [
                'message' => $updateTmpUser->email,
                'message_key' => __( 'api.request_resend_otp_success' ),
                'data' => [
                    'otp_code' => '#DEBUG - ' . $updateTmpUser->otp_code,
                    'tmp_user' => Crypt::encryptString( $updateTmpUser->id ),
                ]
            ] );
        }
    }

    public static function resetPassword( $request ) {

        DB::beginTransaction();

        try {
            $request->merge( [
                'identifier' => Crypt::decryptString( $request->identifier ),
            ] );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'message' =>  __( 'api.invalid_otp' ),
            ], 500 );
        }

        $validator = Validator::make( $request->all(), [
            'identifier' => [ 'required', function( $attribute, $value, $fail ) use ( $request, &$currentOtpAction ) {

                $currentOtpAction = OtpAction::lockForUpdate()
                    ->find( $value );
                if ( !$currentOtpAction ) {
                    $fail( __( 'api.invalid_otp' ) );
                    return false;
                }

                if ( $currentOtpAction->status != 11 ) {
                    $fail( __( 'api.invalid_otp' ) );
                    return false;
                }
                
            } ],
            'password' => [ 'required', Password::min( 8 ), 'confirmed' ],
        ] );

        $attributeName = [
            'password' => __( 'user.password' ),
            'confirm_password' => __( 'user.confirm_password' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $updateUser = User::where( 'email', $currentOtpAction->email )->first();
            $updateUser->password = Hash::make( $request->password );
            $updateUser->save();

            $currentOtpAction->status = 10;
            $currentOtpAction->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
            'message_key' => __( 'api.reset_password_fail' ),
            'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }

        return response()->json( [
            'message_key' => __( 'api.reset_password_success' ),
            'data' => [],
        ] );
    }

    public static function myTeamAjax( $request ){

        $downlines = User::select( [
            'users.id',
            'users.username',
            'users.email',
        ] );

        if( !empty( $request->id ) ){
            $downlines = $downlines->where( 'referral_id', \Helper::decode( $request->id ) );
        }else{
            $downlines = $downlines->where( 'referral_id', auth()->user()->id );
        }

        $downlines = $downlines->get();

        if( $downlines ){
            $downlines->append( [
                'encrypted_id',
            ] );
        }

        return response()->json( [
            'data' => $downlines,
        ] );
    }

    public static function initMyTeam( $request ){

        if( !empty( $request->id ) ){
            $id = Helper::decode( $request->id );
        }else{
            $id = auth()->user()->id;
        }

        $user = User::select( [
            'users.id',
            'users.username',
            'users.email',
        ] )->find( $id );

        $user->append( [
            'encrypted_id',
        ] );

        return $user;
    }

    public static function searchMyTeam( $request ) {

        $user = auth()->user();
        $team = $user->team();
        
        if (!empty($request->user)) {
            $team->where(function ($query) use ($request) {
                $query->where('username', 'like', '%' . $request->user . '%')
                      ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }
    
        $teamMembers = $team->get();

        $teamMembers->append( 'encrypted_id' );
        
        return response()->json($teamMembers);
    }

    public static function _allUsers( $request ) {

        $referral_structure = explode( '|', auth()->user()->referral_structure );

        $users = User::with(['userDetail'])
            ->select('users.id', 'users.username', 'users.email')
            ->where( function ($a) use ( $referral_structure ) {
                $a->where( 'referral_structure', 'like', '%' . auth()->user()->id . '%' )
                    ->orWhereIn( 'id', $referral_structure );
            } )
            ->where( function ($query) use ($request) {
                $query->where('users.email', $request->user );
            })
            ->get();

        foreach( $users as $user ){
            $user->append( [
                'encrypted_id',
            ] );
        }

        $data = [
            'users' => $users,
        ];

        return $data;
    }

    public static function allDownlines( $request ) {

        $users = User::with(['userDetail'])
            ->select('users.id', 'users.username', 'users.email')
            ->where( function ($a) {
                $a->where( 'referral_structure', 'like', '%' . auth()->user()->id . '%' );
            } )
            ->where( function ($query) use ($request) {
                $query->where('users.email', 'like', '%'. $request->user .'%' )
                    ->orWhere( 'users.username', 'like', '%'. $request->user .'%' );
            })
            ->get();

        foreach( $users as $user ){
            $user->append( [
                'encrypted_id',
            ] );
        }

        $data = [
            'users' => $users,
        ];

        return $data;
    }

    // Share
    private static function create( $data ) {

        $createUser = User::create( $data['user'] );

        $data['user_detail']['user_id'] = $createUser->id;

        $createUserDetail = UserDetail::create( $data['user_detail'] );

        if ( $data['user']['referral_id'] ) {
            $referralArray = explode( '|', $data['user']['referral_structure'] );
            $referralLevel = count( $referralArray );
            for ( $i = $referralLevel - 1; $i >= 0; $i-- ) {
                if ( $referralArray[$i] != '-' ) {
                    UserStructure::create( [
                        'user_id' => $createUser->id,
                        'referral_id' => $referralArray[$i],
                        'level' => $referralLevel - $i
                    ] );
                }
            }
        }

        $wallets = \Helper::wallets();
        foreach ( $wallets as $key => $value ) { 
            UserWallet::create( [
                'user_id' => $createUser->id,
                'type' => $key,
                'balance' => 0,
            ] );
        }

        return $createUser;
    }

    private function registerOneSignal( $user_id, $device_type, $register_token ) {
        
        UserDevice::updateOrCreate( 
            [ 'user_id' => $user_id, 'device_type' => 1 ],
            [ 'register_token' => $register_token ]
        );
    }
}