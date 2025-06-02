<?php

namespace App\Services;

use App\Models\{
    MailAction,
    User,
};

use GuzzleHttp\Client;

class MailService {

    protected $data;
    protected $accessToken;

    public function __construct( $data ) {
        $this->data = $data;
        $this->getAccessToken();
    }

    public function getAccessToken() {
        $client = new Client();

        $tenantId = config( 'services.azure.tenant' );
        $clientId = config( 'services.azure.client' );
        $clientSecret = config( 'services.azure.secret' );

        try {
            $response = $client->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'form_params' => [
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'scope'         => 'https://graph.microsoft.com/.default',
                    'grant_type'    => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['access_token'])) {
                return json_encode($data);
            }
            $this->accessToken = $data['access_token'];

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function send() {
        if( isset( $this->data[ 'amount'] ) ){
            $this->data[ 'amount' ] = \Helper::numberFormat( $this->data[ 'amount' ], 2 );
        }
        
        $result = $this->sendClient();
        if( !$result || !isset( $result['status'] ) || $result['status'] != 200 ) {
            return $result;
        }

        return $result;
    }

    private function sendClient() {
        $client = new Client();
        $email = [
            'message' => [
                'subject' => $this->getSubject(),
                'body' => [
                    'contentType' => 'Html',
                    'content' => $this->getView()
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $this->data['email']
                        ]
                    ]
                ]
            ]
        ];

        $senderEmail = 'support@mtic.ltd';

        try {
            $response = $client->post("https://graph.microsoft.com/v1.0/users/{$senderEmail}/sendMail", [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => $email
            ]);
            
            $user = null;
            if( isset( $this->data['name'] ) ) {
                $user = User::where( 'username', $this->data['name'] )->first();
            }
            if( $user ){
                $createMailAction = MailAction::create( [
                    'user_id' => $user->id,
                    'subject' => $this->getSubject(),
                    'email' => $this->data['email'],
                    'data' => json_encode( $this->data ),
                ] );
            }else{
                $createMailAction = MailAction::create( [
                    'subject' => $this->getSubject(),
                    'email' => $this->data['email'],
                    'data' => json_encode( $this->data ),
                ] );
            }

            return [
                'status' => 200,
                'message' => __( 'mail.send_success', [ 'mail' => $this->data['email'] ] )
            ];

        } catch (\Exception $e) {
            
            $user = null;
            if( isset( $this->data['name'] ) ) {
                $user = User::where( 'username', $this->data['name'] )->first();
            }
            if( $user ){
                $createMailAction = MailAction::create( [
                    'user_id' => $user->id,
                    'subject' => $this->getSubject(),
                    'email' => $this->data['email'],
                    'data' => json_encode( $this->data ),
                    'status' => 20,
                ] );
            }else{
                $createMailAction = MailAction::create( [
                    'subject' => $this->getSubject(),
                    'email' => $this->data['email'],
                    'data' => json_encode( $this->data ),
                    'status' => 20,
                ] );
            }

            \Log::error( [
                'status' => 500,
                'message_key' => 'send_mail_client',
                'message' => $e->getMessage()
            ] );

            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getView() {
        switch ( $this->data['type'] ) {
            case 1:
                return view( 'admin.mail.otp', [ 'data' => $this->data ] )->render();
            case 2:
                return view( 'admin.mail.submitKyc', [ 'data' => $this->data ] )->render();
            case 3:
                return view( 'admin.mail.approveKyc', [ 'data' => $this->data ] )->render();
            case 4:
                return view( 'admin.mail.rejectKyc', [ 'data' => $this->data ] )->render();
            case 5:
                return view( 'admin.mail.fund', [ 'data' => $this->data ] )->render();
            case 6:
                return view( 'admin.mail.invest', [ 'data' => $this->data ] )->render();
            case 7:
                return view( 'admin.mail.withdrawal', [ 'data' => $this->data ] )->render();
            case 8:
                return view( 'admin.mail.approveWithdrawal', [ 'data' => $this->data ] )->render();
            case 9:
                return view( 'admin.mail.rejectWithdrawal', [ 'data' => $this->data ] )->render();
            case 10:
                return view( 'admin.mail.terminate', [ 'data' => $this->data ] )->render();
            case 11:
                return view( 'admin.mail.approveTerminate', [ 'data' => $this->data ] )->render();
            case 12:
                return view( 'admin.mail.accountCreated', [ 'data' => $this->data ] )->render();
            case 13:
                return view( 'admin.mail.monthlyReport', [ 'data' => $this->data ] )->render();
            case 14:
                return view( 'admin.mail.deposit', [ 'data' => $this->data ] )->render();
            case 15:
                return view( 'admin.mail.rejectDeposit', [ 'data' => $this->data ] )->render();
            case 16:
                return view( 'admin.mail.createUser', [ 'data' => $this->data ] )->render();
            case 17:
                return view( 'admin.mail.depositMt5', [ 'data' => $this->data ] )->render();
            case 18:
                return view( 'admin.mail.createMt5', [ 'data' => $this->data ] )->render();
            case 19:
                return view( 'admin.mail.withdrawalMt5', [ 'data' => $this->data ] )->render();
            default:
                return '';
        }
    }

    public function getSubject() {
        switch ( $this->data['type'] ) {
            case 1:
                return __( 'user.otp_title' );
            case 2:
                return __( 'user.submit_kyc_title' );
            case 3:
                return __( 'user.approve_kyc_title' );
            case 4:
                return __( 'user.reject_kyc_title' );
            case 5:
                return __( 'user.fund_title' );
            case 6:
                return __( 'user.invest_title' );
            case 7:
                return __( 'user.withdrawal_title' );
            case 8:
                return __( 'user.approve_withdrawal_title' );
            case 9:
                return __( 'user.reject_withdrawal_title' );
            case 10:
                return __( 'user.terminate_title' );
            case 11:
                return __( 'user.approve_terminate_title' );
            case 12:
                return __( 'user.account_created_title' );
            case 13:
                return __( 'user.report_title' );
            case 14:
                return __( 'user.deposit_title' );
            case 15:
                return __( 'user.reject_deposit_title' );
            case 16:
                return __( 'user.create_user_title' );
            case 17:
                return __( 'user.deposit_mt5_title' );
            case 18:
                return __( 'user.create_mt5_title' );
            case 19:
                return __( 'user.withdrawal_mt5_title' );
        }
    }

    public function resend() {
        $result = $this->sendClient();
        return $result;
    }
}