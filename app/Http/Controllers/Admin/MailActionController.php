<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{
    MailActionService,
    Service,
};

class MailActionController extends Controller
{
    public function index() {

        $this->data['header']['title'] = __( 'template.mail_actions' );
        $this->data['content'] = 'admin.mail_action.index';
        $this->data['breadcrumbs'] = [
            'enabled' => true,
            'main_title' => __( 'template.mail_actions' ),
            'title' => __( 'template.list' ),
            'mobile_title' => __( 'template.mail_actions' ),
        ];

        return view( 'admin.main' )->with( $this->data );
    }

    public function allMailActions( Request $request ) {

        return MailActionService::allMailActions( $request );
    }

    public function oneMailAction( Request $request ) {

        return MailActionService::oneMailAction( $request );
    }

    public function resendMail( Request $request ) {

        return MailActionService::resendMail( $request );
    }
}
