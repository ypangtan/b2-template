<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\{
    ActivityLog,
};

use Helper;

use Carbon\Carbon;

class AuditService {

    public function allAudits( $request, $export = false ) {

        $filter = false;

        $log = ActivityLog::select( 'activity_log.*', 'admins.username AS admin_username' );
        $log->leftJoin( 'admins', 'activity_log.causer_id', '=', 'admins.id' );
        $log->where( 'causer_type', 'App\Models\Admin' );
        
        if ( !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $log->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->created_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $log->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;   
        }

        if ( !empty( $request->username ) ) {
            $log->where( 'username', $username );
            $filter = true;
        }
        
        if ( !empty( $moduleName = $request->module_name ) ) {
            $log->where( 'log_name', 'LIKE', "%{$moduleName}%" );
            $filter = true;
        }

        if ( !empty( $actionPerformed = $request->action_performed ) ) {
            $log->where( 'description', 'LIKE', "%{$actionPerformed}%" );
            $filter = true;
        }

        if ( $request->input( 'order.0.column' ) != 0 ) {

            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $log->orderBy( 'created_at', $request->input( 'order.0.dir' ) );
                    break;
            }
        }

        if ( $export == false ) {

            $logCount = $log->count();

            $limit = $request->input( 'length' );
            $offset = $request->input( 'start' );
            
            $logObject = $log->skip( $offset )->take( $limit );
            $logs = $logObject->get();

            $log = ActivityLog::select(
                \DB::raw( 'COUNT(activity_log.id) as total'
            ) );

            if ( !empty( $request->created_date ) ) {
                if ( str_contains( $request->created_date, 'to' ) ) {
                    $dates = explode( ' to ', $request->created_date );
    
                    $startDate = explode( '-', $dates[0] );
                    $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                    
                    $endDate = explode( '-', $dates[1] );
                    $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );
    
                    $log->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
                } else {
    
                    $dates = explode( '-', $request->created_date );
    
                    $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                    $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );
    
                    $log->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
                }
                $filter = true;   
            }

            if ( !empty( $request->username ) ) {
                $log->where( 'username', $username );
                $filter = true;
            }
            
            if ( !empty( $moduleName = $request->module_name ) ) {
                $log->where( 'log_name', 'LIKE', "%{$moduleName}%" );
                $filter = true;
            }
    
            if ( !empty( $actionPerformed = $request->action_performed ) ) {
                $log->where( 'description', 'LIKE', "%{$actionPerformed}%" );
                $filter = true;
            }

            $log = $log->first();

            $data = [
                'logs' => $logs,
                'draw' => $request->draw,
                'recordsFiltered' => $filter ? $logCount : $log->total,
                'recordsTotal' => ActivityLog::select( \DB::raw( 'COUNT(id) as total' ) )->first()->total,
            ];

            return $data;
        }
    }

    public function oneAudit( $request ) {

        $log = ActivityLog::find( $request->id );

        return response()->json( $log );
    }
}