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

        $log = ActivityLog::select( 'activity_log.*', 'admins.username AS admin_username' );
        $log->leftJoin( 'admins', 'activity_log.causer_id', '=', 'admins.id' );
        $log->where( 'causer_type', 'App\Models\Admin' );

        $filterObject = self::filter( $request, $log );
        $log = $filterObject['model'];
        $filter = $filterObject['filter'];

        if ( $request->input( 'order.0.column' ) != 0 ) {
            $dir = $request->input( 'order.0.dir' );
            switch ( $request->input( 'order.0.column' ) ) {
                case 1:
                    $log->orderBy( 'created_at', $dir );
                    break;
            }
        }

        if ( $export == false ) {

            $logCount = $log->count();

            $limit = $request->length;
            $offset = $request->start;
            
            $logs = $log->skip( $offset )->take( $limit )->get();

            $totalRecord = ActivityLog::count();

            $data = [
                'logs' => $logs,
                'draw' => $request->draw,
                'recordsFiltered' => $filter ? $logCount : $totalRecord,
                'recordsTotal' => $totalRecord,
            ];

            return response()->json( $data );
        }
    }

    private function filter( $request, $model ) {

        $filter = false;

        if ( !empty( $request->created_date ) ) {
            if ( str_contains( $request->created_date, 'to' ) ) {
                $dates = explode( ' to ', $request->created_date );

                $startDate = explode( '-', $dates[0] );
                $start = Carbon::create( $startDate[0], $startDate[1], $startDate[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                
                $endDate = explode( '-', $dates[1] );
                $end = Carbon::create( $endDate[0], $endDate[1], $endDate[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            } else {

                $dates = explode( '-', $request->created_date );

                $start = Carbon::create( $dates[0], $dates[1], $dates[2], 0, 0, 0, 'Asia/Kuala_Lumpur' );
                $end = Carbon::create( $dates[0], $dates[1], $dates[2], 23, 59, 59, 'Asia/Kuala_Lumpur' );

                $model->whereBetween( 'activity_log.created_at', [ date( 'Y-m-d H:i:s', $start->timestamp ), date( 'Y-m-d H:i:s', $end->timestamp ) ] );
            }
            $filter = true;   
        }

        if ( !empty( $request->username ) ) {
            $model->where( 'username', $username );
            $filter = true;
        }
        
        if ( !empty( $moduleName = $request->module_name ) ) {
            $model->where( 'log_name', 'LIKE', "%{$moduleName}%" );
            $filter = true;
        }

        if ( !empty( $actionPerformed = $request->action_performed ) ) {
            $model->where( 'description', 'LIKE', "%{$actionPerformed}%" );
            $filter = true;
        }

        return [
            'filter' => $filter,
            'model' => $model,
        ];
    }    

    public function oneAudit( $request ) {

        $log = ActivityLog::find( $request->id );

        return response()->json( $log );
    }
}