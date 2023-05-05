<?php

namespace App\Helpers;

use App\Models\{
    AdministratorNotification,
    AdministratorNotificationSeen,
};

use Hashids\Hashids;
use Carbon\Carbon;

class Helper {

    public static function websiteName() {
        return 'MeCar';
    }

    public static function baseUrl() {
        return url( '/' );
    }

    public static function baseAdminUrl() {
        return url( '/' ) . '/' . self::adminPath();
    }

    public static function adminPath() {
        return 'backoffice';
    }

    public static function baseBranchUrl() {
        return url( '/' ).'/base2_branch';
    }

    public static function assetVersion() {
        return '?v=1.00';
    }

    public static function moduleActions() {

        return [
            'add',
            'view',
            'edit',
            'delete'
        ];
    }

    public static function wallets() {
        return [
            '1' => __( 'wallet.wallet_1' ),
            '2' => __( 'wallet.wallet_2' ),
        ];
    }

    public static function trxTypes() {
        return [
            '1' => __( 'wallet.topup' ),
            '2' => __( 'wallet.refund' ),
            '3' => __( 'wallet.manual_adjustment' ),
        ];
    }

    public static function numberFormat( $number, $decimal, $isRound = false ) {
        if ( $isRound ) {
            return number_format( $number, $decimal );    
        } else {
            return number_format( bcdiv( $number, 1, $decimal ), $decimal );
        }
    }

    public static function hideTimestamp( $model, $columns ) {
        $model->makeHidden( $columns );
    }

    public static function hideTimestampQuery( $table ) {

        $columns = \DB::select( \DB::raw( "SHOW COLUMNS FROM {$table} WHERE FIELD NOT IN ( 'created_at', 'updated_at', 'deleted_at' )" ) );

        return $fields = array_column( $columns , 'Field' );
    }

    public static function showColumnsQuery( $table, $hide_columns ) {

        $columns = \DB::select( \DB::raw( "SHOW COLUMNS FROM {$table} WHERE FIELD IN ( " . ( "'" . implode( "', '", $hide_columns ) . "'" ) . " )" ) );

        return $fields = array_column( $columns , 'Field' );
    }

    public static function hideColumnsQuery( $table, $hide_columns ) {

        $columns = \DB::select( \DB::raw( "SHOW COLUMNS FROM {$table} WHERE FIELD NOT IN ( " . ( "'" . implode( "', '", $hide_columns ) . "'" ) . " )" ) );

        return $fields = array_column( $columns , 'Field' );
    }

    public static function curlGet( $endpoint, $header = array(

    ) ) {

        $curl = curl_init();

        curl_setopt_array( $curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
        ) );

        $response = curl_exec ($curl );
        $error = curl_error( $curl );
        
        curl_close( $curl );

        if ( $error ) {
            return false;
        } else {
            return $response;
        }
    }

    public static function curlPost( $endpoint, $data, $header = array(
        "accept: */*",
        "accept-language: en-US,en;q=0.8",
        "content-type: application/json",
    ) ) {
        
        $curl = curl_init();
        
        curl_setopt_array( $curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $header
        ) );
        
        $response = curl_exec ($curl );
        $error = curl_error( $curl );
        
        curl_close( $curl );
        
        if ( $error ) {
            return false;
        } else {
            return $response;
        }
    }

    public static function exportReport( $html, $model ) {

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString( $html );

        foreach( $spreadsheet->getActiveSheet()->getColumnIterator() as $column ) {
            $spreadsheet->getActiveSheet()->getColumnDimension( $column->getColumnIndex() )->setAutoSize( true );
        }

        $filename = $model . '_' . date( 'ymd' ) . '.xlsx';

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $spreadsheet, 'Xlsx' );
        $writer->save( 'storage/'.$filename );

        $content = file_get_contents( 'storage/'.$filename );

        header( "Content-Disposition: attachment; filename=".$filename );
        unlink( 'storage/'.$filename );
        exit( $content );
    }

    public static function compressImage( $raw_image, $path, $name ) {

        $info = getimagesize( $raw_image );

		switch ( $info['mime'] ) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg( $raw_image );
				break;
			case 'image/png':
				$image = imagecreatefrompng( $raw_image );
				break;
			case 'image/gif':
				$image = imagecreatefromgif ( $raw_image );
				break;
		}

        $exif = @exif_read_data( $raw_image );

        if ( !empty( $exif ) && isset( $exif['Orientation'] ) ) {
            $image = self::image_orientation( $image, $exif['Orientation'] );
        }

        unlink( storage_path() . '/app/public/' . $path . '/' . $name );
        imagejpeg( $image, storage_path() . '/app/public/' . $path . '/' . $name , 65 );
		imagedestroy( $image );
    }

    public static function image_orientation( $image_object, $orientation ) {

        switch ($orientation) {
            case 2:
                $image_object = imageflip($image_object, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $image_object = imagerotate($image_object, 180, 0);
                break;
            case 4:
                imageflip($image_object, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $image_object = imagerotate($image_object, -90, 0);
                imageflip($image_object, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $image_object = imagerotate($image_object, -90, 0);
                break;
            case 7:
                $image_object = imagerotate($image_object, 90, 0);
                imageflip($image_object, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $image_object = imagerotate($image_object, 90, 0); 
                break;
        }

        return $image_object;
    }

    public static function generateInvoice( $order = null ) {

        // Varies on project
        $service_html = '';
        $product_count = 1;
        foreach( $order->items as $i => $s ) {

            $last = '';
            if ( $product_count == count( $order->items ) ) {
                $last = ' last';
            }

            $service_html .= 
            '<tr class="item' . $last . '">
                <td><strong>' . $s->title . '</strong></td>
                <td>' . $s->amount . '</td>
                <td>' . $s->quantity . '</td>
                <td>' . ( number_format( $s->quantity * $s->amount, 2 ) ) . '</td>

            </tr>';
            $product_count++;
        }

        $html = '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>Invoice #' . $order->reference . '</title>
        
                <style>
                    .invoice-box {
                        max-width: 800px;
                        margin: auto;
                        padding: 0px;
                        /* border: 1px solid #eee;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); */
                        font-size: 16px;
                        line-height: 24px;
                        font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
                        color: #555;
                    }
        
                    .invoice-box table {
                        width: 100%;
                        line-height: inherit;
                        text-align: left;
                    }
        
                    .invoice-box table td {
                        padding: 5px;
                        vertical-align: top;
                    }
        
                    .invoice-box table tr td:nth-child(n+2) {
                        text-align: right;
                    }
        
                    .invoice-box table tr.top table td {
                        padding-bottom: 20px;
                    }
        
                    .invoice-box table tr.top table td.title {
                        font-size: 45px;
                        line-height: 45px;
                        color: #333;
                    }
        
                    .invoice-box table tr.information table td {
                        padding-bottom: 40px;
                    }
        
                    .invoice-box table tr.heading td {
                        background: #eee;
                        border-bottom: 1px solid #ddd;
                        font-weight: bold;
                    }
        
                    .invoice-box table tr.details td {
                        padding-bottom: 20px;
                    }
        
                    .invoice-box table tr.item td {
                        border-bottom: 1px solid #eee;
                    }
        
                    .invoice-box table tr.item.last td {
                        border-bottom: none;
                    }
        
                    .invoice-box table tr.discount td {
                        border-top: 2px solid #eee;
                    }

                    .invoice-box table tr.total td:nth-child(2) {
                        font-weight: bold;
                    }
        
                    @media only screen and (max-width: 600px) {
                        .invoice-box table tr.top table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
        
                        .invoice-box table tr.information table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                    }
        
                    /** RTL **/
                    .invoice-box.rtl {
                        direction: rtl;
                        font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
                    }
        
                    .invoice-box.rtl table {
                        text-align: right;
                    }
        
                    .invoice-box.rtl table tr td:nth-child(2) {
                        text-align: left;
                    }
                </style>
            </head>
        
            <body>
                <div class="invoice-box">
                    <table cellpadding="0" cellspacing="0">
                        <tr class="top">
                            <td colspan="4">
                                <table>
                                    <tr>
                                        <td class="title">
                                            <img src="' . __DIR__ . '/logo-01.png" style="width: 100%; max-width: 200px" />
                                        </td>
        
                                        <td>
                                            Invoice #: ' . $order->reference . '<br />
                                            Created: ' . date( 'M d, Y', strtotime( $order->created_at ) ) . '<br />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
        
                        <tr class="information">
                            <td colspan="4">
                                <table>
                                    <tr>
                                        <td>
                                            TcLam Sdn. Bhd.<br />
                                            -<br />
                                            -
                                        </td>
        
                                        <td>
                                            ' . $order->address_detail['name'] . '<br />
                                            ' . $order->address_detail['detail'] . '<br />
                                            ' . $order->postcode . ', ' . $order->address_detail['state'] . '<br />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
        
                        <tr class="heading">
                            <td>Products(s)</td>
                            <td>Cost</td>
                            <td>Quantity</td>
                            <td>Price (MYR)</td>
                        </tr>
        
                        ' . $service_html . '
        
                        <tr class="discount">
                            <td></td>
                            <td></td>
                            <td style="text-align: right">
                                <small>Discount:</small><br>
                                <small>Shipping:</small>
                            </td>
                            <td style="width: 15%;">
                                <small>' . ( $order->discount == 0 ? '0.00' : number_format( '-' . $order->discount, 2 ) ) . '</small><br>
                                <small>' . ( $order->shipping_fee == 0 ? 'FREE' : number_format( $order->shipping_fee, 2 ) ) . '</small>
                            </td>
                        </tr>
                        
                        <tr class="total">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total: ' . number_format( $order->amount, 2 ) . '</td>
                        </tr>
                    </table>
                </div>
            </body>
        </html>';

        $mpdf = new \Mpdf\Mpdf( [ 'tempDir' => 'storage' ] );
        $mpdf->WriteHTML( $html );
        $mpdf->Output( 'storage/invoice/' . $order->reference . '.pdf', 'F' );
    }

    public static function getCountries() {

        return [
            "AF",
            "AX",
            "AL",
            "DZ",
            "AS",
            "AD",
            "AO",
            "AI",
            "AQ",
            "AG",
            "AR",
            "AM",
            "AW",
            "AU",
            "AT",
            "AZ",
            "BS",
            "BH",
            "BD",
            "BB",
            "BY",
            "BE",
            "BZ",
            "BJ",
            "BM",
            "BT",
            "BO",
            "BQ",
            "BA",
            "BW",
            "BV",
            "BR",
            "IO",
            "BN",
            "BG",
            "BF",
            "BI",
            "KH",
            "CM",
            "CA",
            "CV",
            "KY",
            "CF",
            "TD",
            "CL",
            "CN",
            "CX",
            "CC",
            "CO",
            "KM",
            "CG",
            "CD",
            "CK",
            "CR",
            "CI",
            "HR",
            "CU",
            "CW",
            "CY",
            "CZ",
            "DK",
            "DJ",
            "DM",
            "DO",
            "EC",
            "EG",
            "SV",
            "GQ",
            "ER",
            "EE",
            "ET",
            "FK",
            "FO",
            "FJ",
            "FI",
            "FR",
            "GF",
            "PF",
            "TF",
            "GA",
            "GM",
            "GE",
            "DE",
            "GH",
            "GI",
            "GR",
            "GL",
            "GD",
            "GP",
            "GU",
            "GT",
            "GG",
            "GN",
            "GW",
            "GY",
            "HT",
            "HM",
            "VA",
            "HN",
            "HK",
            "HU",
            "IS",
            "IN",
            "ID",
            "IR",
            "IQ",
            "IE",
            "IM",
            "IL",
            "IT",
            "JM",
            "JP",
            "JE",
            "JO",
            "KZ",
            "KE",
            "KI",
            "KP",
            "KR",
            "XK",
            "KW",
            "KG",
            "LA",
            "LV",
            "LB",
            "LS",
            "LR",
            "LY",
            "LI",
            "LT",
            "LU",
            "MO",
            "MK",
            "MG",
            "MW",
            "MY",
            "MV",
            "ML",
            "MT",
            "MH",
            "MQ",
            "MR",
            "MU",
            "YT",
            "MX",
            "FM",
            "MD",
            "MC",
            "MN",
            "ME",
            "MS",
            "MA",
            "MZ",
            "MM",
            "NA",
            "NR",
            "NP",
            "NL",
            "AN",
            "NC",
            "NZ",
            "NI",
            "NE",
            "NG",
            "NU",
            "NF",
            "MP",
            "NO",
            "OM",
            "PK",
            "PW",
            "PS",
            "PA",
            "PG",
            "PY",
            "PE",
            "PH",
            "PN",
            "PL",
            "PT",
            "PR",
            "QA",
            "RE",
            "RO",
            "RU",
            "RW",
            "BL",
            "SH",
            "KN",
            "LC",
            "MF",
            "PM",
            "VC",
            "WS",
            "SM",
            "ST",
            "SA",
            "SN",
            "RS",
            "CS",
            "SC",
            "SL",
            "SG",
            "SX",
            "SK",
            "SI",
            "SB",
            "SO",
            "ZA",
            "GS",
            "SS",
            "ES",
            "LK",
            "SD",
            "SR",
            "SJ",
            "SZ",
            "SE",
            "CH",
            "SY",
            "TW",
            "TJ",
            "TZ",
            "TH",
            "TL",
            "TG",
            "TK",
            "TO",
            "TT",
            "TN",
            "TR",
            "TM",
            "TC",
            "TV",
            "UG",
            "UA",
            "AE",
            "GB",
            "US",
            "UM",
            "UY",
            "UZ",
            "VU",
            "VE",
            "VN",
            "VG",
            "VI",
            "WF",
            "EH",
            "YE",
            "ZM",
            "ZW"
        ];
    }

    public static function columnIndex( $object, $search ) {
        foreach ( $object as $key => $o ) {
            if ( $o['id'] == $search ) {
                return $key;
            }
        }
    }

    public static function encode( $id ) {

        $hashids = new Hashids( config( 'app.key' ) );

        return $hashids->encode( $id );
    }

    public static function decode( $id ) {

        $hashids = new Hashids( config( 'app.key' ) );

        return $hashids->decode( $id )[0];
    }

    public static function administratorNotifications() {

        $notifications = AdministratorNotification::select( 
            'administrator_notifications.*',
            \DB::raw( '( SELECT COUNT(*) FROM administrator_notification_seens AS a WHERE a.an_id = administrator_notifications.id AND a.administrator_id = ' .auth()->user()->id. ' ) as is_read' )
        )->where( function( $query ) {
            $query->where( 'administrator_id', auth()->user()->id );
            $query->orWhere( 'role_id', auth()->user()->role );
        } )->orWhere( function( $query ) {
            $query->whereNull( 'administrator_id' );
            $query->whereNull( 'role_id' );
        } )->orderBy( 'administrator_notifications.created_at', 'DESC' )->get();

        $totalUnread = AdministratorNotificationSeen::where( 'administrator_id', auth()->user()->id )->count();

        $data['total_unread'] = count( $notifications ) - $totalUnread;
        $data['notifications'] = $notifications;

        return $data;
    }

    public static function getDisplayTimeUnit( $createdAt ) {

        $created = new Carbon( $createdAt );
        $now = Carbon::now();

        if ( $created->format( 'd' ) != $now->format( 'd' ) ) {

            $difference = $created->startOfDay()->diff( $now->startOfDay() )->days;
            if ( $difference == 1 ) {
                return __( 'template.yesterday' );
            } else {
                return __( 'template.' . strtolower( $created->format( 'l' ) ) );
            }

        } else {
            return $created->setTimezone( 'Asia/Kuala_Lumpur' )->format( 'H:i' );
        }
    }
}