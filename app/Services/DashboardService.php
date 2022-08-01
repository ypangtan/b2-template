<?php

namespace App\Services;

use App\Models\{
    Order,
    User,
    VoucherUsage
};

use Helper;

class DashboardService {

    public function dashboardDatas( $request ) {

        // // Orders By Status
        // $raw_order_statuses = Order::selectRaw( 'COUNT(id) AS TOTAL_ORDER, status AS STATUS' )->groupBy( 'status' )->get();
        // $order_statuses = [];
        // foreach( $raw_order_statuses as $os ) {   
        //     $order_statuses[$os->STATUS] = $os;
        // }
        // foreach( [ 'pending', 'processing', 'shipped', 'completed', 'payment_failed' ] as $key ) {
        //     if( isset( $order_statuses[$key] ) ) {
        //         $order_statuses[$key] = $order_statuses[$key];
        //     } else {
        //         $order_statuses[$key] = [ 'TOTAL_ORDER' => 0, 'STATUS' => $key ];
        //     }
        // }
        // $order_statuses_sorted = [];
        // foreach( [ 'pending', 'processing', 'shipped', 'completed', 'payment_failed' ] as $key ) {
        //     $order_statuses_sorted[$key] = $order_statuses[$key];
        // }
        // // ksort( $order_statuses );
        // $total_order = Order::count();
        $data['order_statuses'] = [];
        $data['total_order'] = 0;

        // // Voucher Used
        // $voucher_usages = VoucherUsage::selectRaw( 'COUNT(*) AS "TOTAL_USED", SUM(voucher_usages.amount) AS "USAGE", voucher_usages.voucher_id' )->groupBy( 'voucher_id' )->orderBy( 'TOTAL_USED', 'DESC' )->limit( 5 )->with( [ 'voucher' => function( $query ) {
        //     $query->withTrashed();
        // } ] )->get();
        $data['voucher_usages'] = [];

        return $data;
    }

    public function totalDatas( $request ) {

        // $today = date( 'Y-m-d' );
        // $last_date = new \DateTime( $today );
        // $last_date->modify( '-1 month' );
        // $last_month_first = $last_date->modify( 'first day of this month' )->format( 'Y-m-d' );
        // $last_month_last = $last_date->modify( 'last day of this month' )->format( 'Y-m-d' );

        // $this_date = new \DateTime( $today );
        // $this_month_first = $this_date->modify( 'first day of this month' )->format( 'Y-m-d' );
        // $this_month_last = $this_date->modify( 'last day of this month' )->format( 'Y-m-d' );

        // $yesterday = new \DateTime( $today );
        // $yesterday = $yesterday->modify( 'yesterday' )->format( 'Y-m-d' );

        // // New User
        // $users_last = User::whereBetween( 'created_at', [ $last_month_first . ' 00:00:00' , $last_month_last . ' 23:59:59' ] )->count();
        // $users_this = User::whereBetween( 'created_at', [ $this_month_first . ' 00:00:00' , $this_month_last . ' 23:59:59' ] )->count();
        $data['users_this'] = 0;
        $data['new_user_percent'] = 0;

        // // Earnings
        // $earnings_last = Order::selectRaw( 'SUM(amount) AS EARNINGS_LAST' )->whereBetween( 'created_at', [ $last_month_first . ' 00:00:00' , $last_month_last . ' 23:59:59' ] )->get();
        // $earnings_this = Order::selectRaw( 'SUM(amount) AS EARNINGS_THIS' )->whereBetween( 'created_at', [ $this_month_first . ' 00:00:00' , $this_month_last . ' 23:59:59' ] )->get();
        $data['earnings_this'] = 0;
        $data['new_earning_percent'] = 0;

        // $orders_yesterday = Order::whereBetween( 'created_at', [ $yesterday . ' 00:00:00', $yesterday . ' 23:59:59' ] )->count();
        // $orders_today = Order::whereBetween( 'created_at', [ $today . ' 00:00:00', $today . ' 23:59:59' ] )->count();
        $data['orders_today'] = 0;
        $data['today_order_percent'] = 0;

        // $orders_last_month = Order::whereBetween( 'created_at', [ $last_month_first . ' 00:00:00' , $last_month_last . ' 23:59:59' ] )->count();
        // $orders_this_month = Order::whereBetween( 'created_at', [ $this_month_first . ' 00:00:00' , $this_month_last . ' 23:59:59' ] )->count();
        $data['orders_this_month'] = 0;
        $data['this_month_order_percent'] = 0;

        return response()->json( $data );
    }

    public function monthlySales( $request ) {

        $months = [];
        $earnings = [];
        for( $x = 6; $x >= 0; $x-- ) {

            $month = strtotime( date( 'Y-m' ).' -' . $x . ' month' );

            $this_date = new \DateTime( date( 'Y-m-d', $month ) );
            $this_month_first = $this_date->modify( 'first day of this month' )->format( 'Y-m-d' );
            $this_month_last = $this_date->modify( 'last day of this month' )->format( 'Y-m-d' );

            $earnings_this = Order::selectRaw( 'SUM(amount) AS EARNINGS_THIS' )->whereBetween( 'created_at', [ $this_month_first . ' 00:00:00' , $this_month_last . ' 23:59:59' ] )->get();
            
            array_push( $months, __( 'dashboard.' . date( 'M', $month ) ) );
            array_push( $earnings, $earnings_this[0]['EARNINGS_THIS'] == null ? 0 : $earnings_this[0]['EARNINGS_THIS'] );
        }

        return response()->json( [ 'months' => $months, 'earnings' => $earnings ] );
    }

    private function calculatePercentage( $this_, $last ) {

        if( $this_ == 0 && $last == 0 ) {
            $d = 0;
            return $d;
        }

        if( $last == 0 ) {
            $d = 100;
        } else {
            $d = $this_ - $last;
            $d = $d / $last * 100;
        }

        return number_format( $d, 2 );
    }
}