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
    public static function allWallets( $request, $export = false ) {

        $userWallet = UserWallet::with( [
            'user',
            'user.userDetail',
        ] )->select( 'user_wallets.*' );

        $filterObject = self::filterWallet( $request, $userWallet );
        $userWallet = $filterObject['model'];
        $filter = $filterObject['filter'];

        $userWallet->orderBy( 'user_wallets.user_id', 'DESC' )->orderBy( 'user_wallets.type', 'ASC' );

        $userWalletCount = $userWallet->count();

        $limit = $request->length;
        $offset = $request->start;

        $userWallets = !$export ? $userWallet->skip( $offset )->take( $limit )->get() : $userWallet->get();

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
            'recordsTotal' => $filter ? UserWallet::where( 'user_wallets.type', '!=', 3 )->count() : $userWalletCount,
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
            $model->where( function( $query ) use ( $request ) {
                $query->whereHas( 'user', function( $query ) use ( $request ) {
                    $query->where( 'users.email', 'LIKE', '%' . $request->user . '%' );
                } );
                $query->orWhereHas( 'user.userDetail', function( $query ) use ( $request ) {
                    $query->where( 'user_details.fullname', 'LIKE', '%' . $request->user . '%' );
                } );
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

    public static function exportWallets( $request ) {

        $wallets = self::allWallets( $request, true );

        $walletLastUpdated = $wallets['user_wallets']->max( 'updated_at' );

        $html = '<table>';
        $html .= '
        <thead>
            <tr>
                <th colspan="2"><strong>' . __( 'datatables.last_updated_at' ) . '</strong></th>
                <th><strong>' . Carbon::parse( $walletLastUpdated )->toDateTimeString() . '</strong></th>
            </tr>
            <tr>
                <th colspan="4"></th> <!-- Empty row for spacing -->
            </tr>
            <tr>
                <th><strong>' .__( 'datatables.no' ). '</strong></th>
                <th><strong>' .__( 'user.user' ). '</strong></th>
                <th><strong>' .__( 'user.email' ). '</strong></th>
                <th><strong>' .__( 'wallet.wallet' ). '</strong></th>
                <th><strong>' .__( 'wallet.balance' ). '</strong></th>
            </tr>
        </thead>
        ';
        $html .= '<tbody>';

        $walletType = Helper::wallets();

        $totalAmount = 0;

        foreach ( $wallets[ 'user_wallets' ] as $key => $wallet ) {

            $html .=
            '
            <tr>
                <td>' . ( intval( $key ) + 1 ) . '</td>
                <td>' . ( $wallet->user->userDetail ? $wallet->user->userDetail->fullname : '-' ) . '</td>
                <td>' . $wallet->user->email . '</td>
                <td>' . $walletType[$wallet->type] . '</td>
                <td>' . Helper::numberFormat( $wallet->balance, 2 ) . '</td>
            </tr>
            ';

            $totalAmount += $wallet->balance;
        }

        $html .= '
            <tr>
                <td colspan="3"></td>
                <td><strong>' . __( 'datatables.grand_total' ) . '</strong></td>
                <td><strong>' . Helper::numberFormat( $totalAmount, 2 ) . '</strong></td>
                <td colspan="3"></td>
            </tr>
        ';
        
        $html .= '</tbody></table>';

        Helper::exportReport( $html, 'User_Wallets' );
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

    public static function allWalletTransactions( $request, $export = false ) {

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

        $transaction->orderBy( 'id', 'DESC' );

        $transactionCount = $transaction->count();

        $limit = $request->length;
        $offset = $request->start;

        $transactions = !$export ? $transaction->skip( $offset )->take( $limit )->get() : $transaction->get();

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

        $model->where( 'status', 10 );

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
            $model->where( function( $query ) use ( $request ) {
                $query->whereHas( 'user', function( $query ) use ( $request ) {
                    $query->where( 'users.email', 'LIKE', '%' . $request->user . '%' );
                } );
                $query->orWhereHas( 'user.userDetail', function( $query ) use ( $request ) {
                    $query->where( 'user_details.fullname', 'LIKE', '%' . $request->user . '%' );
                } );
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

    public static function exportWalletTransactions( $request ) {

        $walletTransactions = self::allWalletTransactions( $request, true );

        $html = '<table>';
        $html .= '
        <thead>
            <tr>
                <th><strong>' .__( 'datatables.no' ). '</strong></th>
                <th><strong>' .__( 'datatables.created_date' ). '</strong></th>
                <th><strong>' .__( 'user.user' ). '</strong></th>
                <th><strong>' .__( 'user.email' ). '</strong></th>
                <th><strong>' .__( 'wallet.wallet' ). '</strong></th>
                <th><strong>' .__( 'wallet.transaction_type' ). '</strong></th>
                <th><strong>' .__( 'wallet.remark' ). '</strong></th>
                <th><strong>' .__( 'wallet.amount' ). '</strong></th>
            </tr>
        </thead>
        ';
        $html .= '<tbody>';

        $walletType = Helper::wallets();
        $transactionType = Helper::trxTypes();

        foreach ( $walletTransactions[ 'transactions' ] as $key => $walletTransaction ) {

            $html .=
            '
            <tr>
                <td>' . ( intval( $key ) + 1 ) . '</td>
                <td>' . $walletTransaction->created_at . '</td>
                <td>' . ( $walletTransaction->user->userDetail ? $walletTransaction->user->userDetail->fullname : '-' ) . '</td>
                <td>' . $walletTransaction->user->email . '</td>
                <td>' . $walletType[$walletTransaction->type] . '</td>
                <td>' . $transactionType[$walletTransaction->transaction_type] . '</td>
                <td>' . $walletTransaction->converted_remark . '</td>
                <td>' . Helper::numberFormat ( abs( $walletTransaction->amount ), 2 ) . '</td>
            </tr>
            ';

        }
        
        $html .= '</tbody></table>';

        Helper::exportReport( $html, 'Wallet_Transactions' );
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

    // Member site
    public static function getAssets( $request ) {

        $wallets = [];

        foreach ( Helper::walletInfos() as $key => $wallet ) {

            $wallets[] = [
                'id' => $key,
                'name' => __( 'wallet.wallet_' . $key ),
                'balance' => $wallet,
            ];
        }

        return [
            'data' => $wallets,
        ];
    }

    public static function getAssetHistories( $request ) {

        $transactions = UserWalletTransaction::where( 'user_id', auth()->user()->id )
            ->where( 'status', 10 )
            ->when( $request->type != '', function( $query ) {
                $query->where( 'type', $request->type );
            } )
            ->when( $request->transaction_type != '', function( $query ) {
                $query->where( 'transaction_type', $request->transaction_type );
            } )
            ->orderBy( 'created_at', 'DESC' )
            ->orderBy( 'id', 'DESC' )
            ->paginate( $request->per_page ? $request->per_page : 10 );
            
        $transactions->each( function( $t ) {
            $t->append( [
                'transaction_type_name',
                'converted_remark',
            ] );
        } );

        return $transactions;
    }
}