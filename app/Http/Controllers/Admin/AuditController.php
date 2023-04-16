<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    AuditService,
};

class AuditController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.audit_logs' );
        $this->data['content'] = 'admin.audit.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.audit_logs' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.audit_logs' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allAudits( Request $request ) {

        return AuditService::allAudits( $request );
    }

    public function oneAudit( Request $request ) {

        return AuditService::oneAudit( $request );
    }
}
