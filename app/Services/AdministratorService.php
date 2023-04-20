<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    DB,
    Hash,
    Validator,
};

use Illuminate\Validation\Rules\Password;

use App\Models\{
    Administrator,
    AdminNotificationSeen,
    Role as RoleModel
};

use App\Rules\CheckASCIICharacter;

use PragmaRX\Google2FAQRCode\Google2FA;

use Helper;

use Carbon\Carbon;

class AdministratorService {
    
    public static function allAdministrators( $request ) {

        $administrator = Administrator::with( [ 'role' ] )->select( 'administrators.*' );

        $filterObject = self::filter( $request, $administrator );
        $administrator = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $administrator->orderBy( 'created_at', $dir );
                    break;
                case 2:
                    $administrator->orderBy( 'name', $dir );
                    break;
                case 3:
                    $administrator->orderBy( 'email', $dir );
                    break;
                case 4:
                    $administrator->orderBy( 'role', $dir );
                    break;
            }
        }

        $administratorCount = $administrator->count();

        $limit = $request->length;
        $offset = $request->start;

        $administrators = $administrator->skip( $offset )->take( $limit )->get();

        if ( $administrators ) {
            $administrators->append( [
                'encrypted_id',
            ] );
        }

        $totalRecord = Administrator::count();

        $data = [
            'administrators' => $administrators,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $administratorCount : $totalRecord,
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

        if ( !empty( $request->role ) ) {
            $model->where( 'administrators.role', $request->role );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneAdministrator( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        return Administrator::find( $request->id );
    }

    public static function createAdministrator( $request ) {

        $request->validate( [
            'username' => [ 'required', 'max:25', 'unique:administrators,username', 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'max:25', 'unique:administrators,email', 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'fullname' => [ 'required' ],
            'role' => [ 'required' ],
            'password' => [ 'required', Password::min( 8 ) ],
        ] );

        DB::beginTransaction();
        
        try {

            $roleModel = RoleModel::find( $request->role );

            $createAdmin = Administrator::create( [
                'username' => strtolower( $request->username ),
                'email' => strtolower( $request->email ),
                'name' => $request->fullname,
                'role' => $request->role,
                'password' => Hash::make( $request->password ),
            ] );

            $createAdmin->syncRoles( [ $roleModel->name ] );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'administrator.administrator_created' ),
        ] );
    }

    public static function updateAdministrator( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $request->validate( [
            'username' => [ 'required', 'max:25', 'unique:administrators,username,' . $request->id, 'alpha_dash', new CheckASCIICharacter ],
            'email' => [ 'required', 'max:25', 'unique:administrators,email,' . $request->id, 'email', 'regex:/(.+)@(.+)\.(.+)/i', new CheckASCIICharacter ],
            'fullname' => [ 'required' ],
            'role' => [ 'required' ],
            'password' => [ 'nullable', Password::min( 8 ) ],
        ] );

        DB::beginTransaction();
        
        try {

            $updateAdministrator = Administrator::find( $request->id );
            $updateAdministrator->id = $request->id;
            $updateAdministrator->username = strtolower( $request->username );
            $updateAdministrator->email = strtolower( $request->email );
            $updateAdministrator->name = $request->fullname;
            $updateAdministrator->role = $request->role;

            if ( !empty( $request->password ) ) {
                $updateAdministrator->password = Hash::make( $request->password );
            }

            $role_model = RoleModel::find( $request->role );
            $updateAdministrator->syncRoles( [ $role_model->name ] );
            $updateAdministrator->save();

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'administrator.administrator_updated' ),
        ] );
    }

    public static function verifyCode( $request ) {

        $request->validate( [
            'authentication_code' => [ 'bail', 'required', 'numeric', 'digits:6', function( $attribute, $value, $fail ) {

                $google2fa = new Google2FA();

                $secret = \Crypt::decryptString( auth()->user()->mfa_secret );
                $valid = $google2fa->verifyKey( $secret, $value );
                if ( !$valid ) {
                    $fail( __( 'setting.invalid_code' ) );
                }
            } ],
        ] );

        session( [
            'mfa-ed' => true
        ] );

        activity()
            ->useLog( 'administrators' )
            ->withProperties( [
                'attributes' => [
                    'new_login' => date( 'Y-m-d H:i:s' ),
                ]
            ] )
            ->log( 'admin login' );

        return response()->json( [
            'status' => true,
        ] );
    }

    public static function logoutLog() {

        activity()
            ->useLog( 'administrators' )
            ->withProperties( [
                'attributes' => [
                    'logout' => date( 'Y-m-d H:i:s' ),
                ]
            ] )
            ->log( 'admin logout' );
    }

    public static function updateNotificationSeen( $request ) {

        AdminNotificationSeen::firstOrCreate( [
            'admin_notification_id' => $request->id,
            'admin_id' => auth()->user()->id,
        ] );
    }
}