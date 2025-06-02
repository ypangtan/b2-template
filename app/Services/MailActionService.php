<?php

namespace App\Services;

use App\Models\{
    AdministratorNotification,
    MailAction,
    Contract,
    InvestmentDocument,
    UserContract,
    ContractProfit,
    SecurityPinAction,
    UserWallet,
    User,
    UserNotification,
    UserNotificationUser,
    UserWalletTransaction,
};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Artisan,
    Crypt,
    DB,
    Validator,
    Storage,
};
use Carbon\Carbon;

class MailActionService {

    public static function allMailActions( $request ) {

        $mail_action = MailAction::with( [
            'user'
        ] )->select( 'mail_actions.*' );

        $filterObject = self::filter( $request, $mail_action );
        $mail_action = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                case 2:
                    $mail_action->orderBy( 'created_at', $dir );
                    break;
            }
        }

        $mail_actionCount = $mail_action->count();

        $limit = $request->length;
        $offset = $request->start;

        $mail_actions = $mail_action->skip( $offset )->take( $limit )->get();

        $mail_actions->append( [
            'encrypted_id',
        ] );

        $mail_action = MailAction::select(
            DB::raw( 'COUNT(mail_actions.id) as total',
        ) );

        $filterObject = self::filter( $request, $mail_action );
        $mail_action = $filterObject['model'];
        $filter = $filterObject['filter'];

        $mail_action = $mail_action->first();

        $data = [
            'mail_actions' => $mail_actions,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $mail_actionCount : $mail_action->total,
            'recordsTotal' => $filter ? $mail_action->total : $mail_actionCount,
        ];

        return $data;
    }

    private static function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->submission_date ) ) {
            if ( str_contains( $request->submission_date, 'to' ) ) {
                $dates = explode( ' to ', $request->submission_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'mail_actions.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->submission_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'mail_actions.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;
        }

        if( !empty( $request->user ) ){
            $model->whereHas( 'user', function( $query ) use ( $request ) {
                $query->where( 'username', 'like', '%' . $request->user . '%' )
                    ->orWhere( 'email', 'like', '%' . $request->user . '%' );
            } );
            $filter = true;
        }

        if ( !empty( $request->subject ) ) {
            $model->where( 'mail_actions.subject', 'like', '%' . $request->subject . '%' );
            $filter = true;
        }

        if ( !empty( $request->email ) ) {
            $model->where( 'mail_actions.email', 'like', '%' . $request->email . '%' );
            $filter = true;
        }

        if ( !empty( $request->status ) ) {
            $model->where( 'mail_actions.status', $request->status );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneMailAction( $request ) {

        $request->merge( [
            'id' => \Helper::decode( $request->id ),
        ] );

        $mail_action = MailAction::with( 'user' )->find( $request->id );

        if ( $mail_action ) {
            $mail_action->append( [
                'encrypted_id',
                'mail'
            ] );
        }

        return $mail_action;
    }

    public static function resendMail( $request ) {
        $request->merge( [
            'id' => \Helper::decode( $request->id )
        ] );

        try {
            $mail = MailAction::find( $request->id );
            if( $mail ){
                $data = json_decode( $mail->data, true );
                $data['email'] = $mail->email;
                $service = new MailService( $data );
                $response = $service->resend();
                if( !$response || !isset( $response['status'] ) || $response['status'] != 200 ){
                    return response()->json( $response, 500 );
                }
                return response()->json( $response );
            }

            return response()->json( [
                'status' => 500,
                'message' => __( 'mail_action.invalid_mail' )
            ], 500 );

        } catch (\Exception $e) {

            return response()->json( [
                'status' => 500,
                'message' => $e->getMessage()
            ], 500 );
        }
    }

}