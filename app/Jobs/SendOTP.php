<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Helper;

class SendOTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $to, $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $to, $body )
    {
        $this->to = $to;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = config( 'services.sms.sms_url' );

        $request = array(
            'api_key' => config( 'services.sms.api_key' ),
            'action' => 'send',
            'to' => $this->to,
            'msg' => $this->body,
            'sender_id' => 'CLOUDSMS',
            'content_type' => 1,
            'mode' => 'shortcode',
            'campaign' => 'Uncle Roger'
        );

        $sendSMS = Helper::curlGet( $url.http_build_query( $request ) );

    }
}
