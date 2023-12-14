<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    DB,
    Validator,
};

use App\Models\{
    UserWallet,
    UserWalletTransaction,
};

use Helper;

use Carbon\Carbon;

class WalletService
{
    public static function allWallets( $request ) {

        $userWallet = UserWallet::with( [
            'user',
            'user.userDetail',
        ] )->select( 'user_wallets.*' );

        $filterObject = self::filterWallet( $request, $userWallet );
        $userWallet = $filterObject['model'];
        $filter = $filterObject['filter'];

        $userWallet->orderBy( 'user_wallets.id', 'DESC' )->orderBy( 'user_wallets.type', 'ASC' );

        $userWalletCount = $userWallet->count();

        $limit = $request->length;
        $offset = $request->start;

        $userWallets = $userWallet->skip( $offset )->take( $limit )->get();

        $pageTotalAmount1 = 0;
        $userWallets->each( function( $uw ) use ( &$pageTotalAmount1 ) {
            $pageTotalAmount1 += $uw->balance;
        } );
        $userWallets->append( [
            'listing_balance',
            'encrypted_id',
        ] );

        $userWallet = UserWallet::select(
            DB::raw( 'COUNT(user_wallets.id) as total,
            SUM(user_wallets.balance) as grandTotal1'
        ) );

        $filterObject = self::filterWallet( $request, $userWallet );
        $userWallet = $filterObject['model'];
        $filter = $filterObject['filter'];

        $userWallet = $userWallet->first();

        $data = [
            'user_wallets' => $userWallets,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $userWalletCount : $userWallet->total,
            'recordsTotal' => $filter ? UserWallet::count() : $userWalletCount,
            'subTotal' => [
                Helper::numberFormat( $pageTotalAmount1, 2, true )
            ],
            'grandTotal' => [ 
                Helper::numberFormat( $userWallet->grandTotal1, 2, true )
            ],
        ];

        return $data;
    }

    private static function filterWallet( $request, $model ) {

        $filter = false;

        if ( !empty( $request->user ) ) {
            $model->whereHas( 'user', function( $query ) use ( $request ) {
                $query->where( 'users.email', $request->user );
            } );
            $model->orWhereHas( 'user.userDetail', function( $query ) use ( $request ) {
                $query->where( 'user_details.fullname', $request->user );
            } );
            $filter = true;
        }

        if ( !empty( $request->wallet ) ) {
            $model->where( 'user_wallets.type', $request->wallet );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function oneWallet( $request ) {

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $userWallet = UserWallet::with( [ 'user' ] )->find( $request->id );

        if ( $userWallet ) {
            $userWallet->append( [
                'listing_balance',
                'encrypted_id',
            ] );
        }

        return response()->json( $userWallet );
    }

    public static function updateWallet( $request ) {

        DB::beginTransaction();

        $request->merge( [
            'id' => Helper::decode( $request->id ),
        ] );

        $validator = Validator::make( $request->all(), [
            'amount' => [ 'required', 'numeric' ],
            'remark' => [ 'required', 'string' ],
        ] );

        $attributeName = [
            'amount' => __( 'wallet.amount' ),
            'remark' => __( 'wallet.remark' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }
        
        $validator->setAttributeNames( $attributeName )->validate();

        try {

            $userWallet = UserWallet::lockForUpdate()->find( $request->id );
            self::transact( $userWallet, [
                'amount' => $request->amount,
                'remark' => $request->remark,
                'type' => $userWallet->type,
                'transaction_type' => 10,
            ] );

            DB::commit();

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.wallets' ) ) ] ),
        ] );
    }

    public static function updateWalletMultiple( $request ) {

        $validator = Validator::make( $request->all(), [
            'amount' => [ 'required', 'numeric' ],
            'remark' => [ 'required', 'string' ],
        ] );

        $attributeName = [
            'amount' => __( 'wallet.amount' ),
            'remark' => __( 'wallet.remark' ),
        ];

        foreach ( $attributeName as $key => $aName ) {
            $attributeName[$key] = strtolower( $aName );
        }

        $validator->setAttributeNames( $attributeName )->validate();

        try {

            foreach ( $request->ids as $id ) {

                DB::beginTransaction();

                $userWallet = UserWallet::lockForUpdate()->find( $id );
                self::transact( $userWallet, [
                    'amount' => $request->amount,
                    'remark' => $request->remark,
                    'type' => $userWallet->type,
                    'transaction_type' => 10,
                ] );

                DB::commit();
            }

        } catch ( \Throwable $th ) {

            DB::rollback();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine(),
            ], 500 );
        }

        return response()->json( [
            'message' => __( 'template.x_updated', [ 'title' => Str::singular( __( 'template.wallets' ) ) ] ),
        ] );
    }

    public static function allWalletTransactions( $request ) {

        $transaction = UserWalletTransaction::with([
            'user',
            'user.userDetail',
        ] )->select( 'user_wallet_transactions.*' );

        $filterObject = self::filterTransaction( $request, $transaction );
        $transaction = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $transaction->orderBy( 'created_at', $dir );
                    break;
            }
        }

        $transactionCount = $transaction->count();

        $limit = $request->length;
        $offset = $request->start;

        $transactions = $transaction->skip( $offset )->take( $limit )->get();

        $pageTotalAmount1 = 0;
        $transactions->each( function( $t ) use ( &$pageTotalAmount1 ) {
            $pageTotalAmount1 += $t->amount;
        } );
        $transactions->append( [
            'converted_remark',
            'listing_amount',
        ] );

        $transaction = UserWalletTransaction::select(
            DB::raw( 'COUNT(user_wallet_transactions.id) as total,
            SUM(user_wallet_transactions.amount) as grandTotal1'
        ) );

        $filterObject = self::filterTransaction( $request, $transaction );
        $transaction = $filterObject['model'];
        $filter = $filterObject['filter'];

        $transaction = $transaction->first();

        $data = [
            'transactions' => $transactions,
            'draw' => $request->draw,
            'recordsFiltered' => $filter ? $transactionCount : $transaction->total,
            'recordsTotal' => $filter ? UserWalletTransaction::where( 'status', 10 )->count() : $transactionCount,
            'subTotal' => [
                Helper::numberFormat( $pageTotalAmount1, 2, true )
            ],
            'grandTotal' => [ 
                Helper::numberFormat( $transaction->grandTotal1, 2, true )
            ],
        ];

        return $data;
    }

    private static function filterTransaction( $request, $model ) {

        $filter = false;

        if (  !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'user_wallet_transactions.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );                
            } else {

                $dates = explode( '-', $request->created_date );
    
                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'user_wallet_transactions.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }

            $filter = true;
        }

        if ( !empty( $request->user ) ) {
            $model->whereHas( 'user', function( $query ) use ( $request ) {
                $query->where( 'users.email', $request->user );
            } );
            $model->orWhereHas( 'user.userDetail', function( $query ) use ( $request ) {
                $query->where( 'user_details.fullname', $request->user );
            } );
            $filter = true;
        }

        if ( !empty( $request->wallet ) ) {
            $model->where( 'user_wallet_transactions.type', $request->wallet );
            $filter = true;
        }

        if ( !empty( $request->transaction_type ) ) {
            $model->where( 'user_wallet_transactions.transaction_type', $request->transaction_type );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }

    public static function transact( UserWallet $userWallet, $data ) {

        $openingBalance = $userWallet->balance;

        $userWallet->balance += $data['amount'];
        $userWallet->save();

        $createUserWalletTransaction = UserWalletTransaction::create( [
            'user_wallet_id' => $userWallet->id,
            'user_id' => $userWallet->user_id,
            'opening_balance' => $openingBalance,
            'amount' => $data['amount'],
            'closing_balance' => $userWallet->balance,
            'remark' => isset( $data['remark'] ) ? $data['remark'] : null,
            'type' => $userWallet->type,
            'transaction_type' => $data['transaction_type'],
        ] );

        return $createUserWalletTransaction;
    }
}