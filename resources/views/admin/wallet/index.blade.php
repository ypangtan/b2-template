<?php
$wallet_topup = 'wallet_topup';
?>

<?php
array_unshift( $data['wallet'], [ 'title' => __( 'datatables.all_x', [ 'title' => __( 'wallet.wallet' ) ] ), 'value' => '' ] );
$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'wallet.username' ) ] ),
        'id' => 'username',
        'title' => __( 'wallet.username' ),
    ],
    [
        'type' => 'select',
        'options' => $data['wallet'],
        'id' => 'wallet',
        'title' => __( 'wallet.wallet' ),
    ],
    [
        'type' => 'default',
        'id' => 'balance',
        'title' => __( 'wallet.balance' ),
    ],
    [
        'type' => 'default',
        'id' => 'dt_action',
        'title' => __( 'datatables.action' ),
    ],
];
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="wallet_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<div class="modal fade" id="wallet_topup_form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __( 'wallet.adjust_balance' ) }}</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <label for="{{ $wallet_topup }}_username" class="col-sm-5 col-form-label">{{ __( 'wallet.username' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control-plaintext" id="{{ $wallet_topup }}_username" readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $wallet_topup }}_balance" class="col-sm-5 col-form-label">{{ __( 'wallet.balance' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control-plaintext" id="{{ $wallet_topup }}_balance" readonly>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $wallet_topup }}_amount" class="col-sm-5 col-form-label">{{ __( 'wallet.amount' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $wallet_topup }}_amount">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $wallet_topup }}_remark" class="col-sm-5 col-form-label">{{ __( 'wallet.remark' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $wallet_topup }}_remark">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="{{ $wallet_topup }}_id">
            <div class="modal-footer">
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button type="button" class="btn btn-sm btn-primary" id="{{ $wallet_topup }}_submit">{{ __( 'template.confirm' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
        
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var walletMapper = {
        '1': '{{ __( 'wallet.wallet_1' ) }}',
        '2': '{{ __( 'wallet.wallet_2' ) }}',
        },
        dt_table,
        dt_table_name = '#wallet_table',
        dt_table_config = {
            language: {
                'lengthMenu': '{{ __( "datatables.lengthMenu" ) }}',
                'zeroRecords': '{{ __( "datatables.zeroRecords" ) }}',
                'info': '{{ __( "datatables.info" ) }}',
                'infoEmpty': '{{ __( "datatables.infoEmpty" ) }}',
                'infoFiltered': '{{ __( "datatables.infoFiltered" ) }}',
                'paginate': {
                    'previous': '{{ __( "datatables.previous" ) }}',
                    'next': '{{ __( "datatables.next" ) }}',
                }
            },
            ajax: {
                url: '{{ route( 'admin.wallet.allWallets' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'user_wallets',
            },
            lengthMenu: [[10, 25],[10, 25]],
            order: false,
            columns: [
                { data: null },
                { data: 'user.username' },
                { data: 'type' },
                { data: 'listing_balance' },
                { data: 'encrypted_id' },
            ],
            columnDefs: [
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "dt_no" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return table_no += 1;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "username" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "wallet" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return walletMapper[data];
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "balance" ) }}' ),
                    orderable: false,
                    className: 'text-end',
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {
                        @can( 'edit wallets' )
                        return '<strong class="dt-edit table-action link-primary" data-id="' + data + '">{{ __( 'datatables.edit' ) }}</strong>';
                        @else
                        return '-';
                        @endcan
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    document.addEventListener( 'DOMContentLoaded', function() {

        let wt = '#{{ $wallet_topup }}',
            wtm = new bootstrap.Modal( document.getElementById( 'wallet_topup_form' ) );

        $( document ).on( 'click', '.dt-edit', function() {

            let id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.wallet.oneWallet' ) }}',
                type: 'POST',
                data: { id, '_token': '{{ csrf_token() }}', },
                success: function( response ) {
                    
                    $( wt + '_id' ).val( response.encrypted_id );
                    $( wt + '_username' ).val( response.user.username );
                    $( wt + '_balance' ).val( response.listing_balance );

                    wtm.toggle();
                },
            } );
        } );

        $( wt + '_submit' ).click( function() {

            $.ajax( {
                url: '{{ route( 'admin.wallet.updateWallet' ) }}',
                type: 'POST',
                data: {
                    'id': $( wt + '_id' ).val(),
                    'amount': $( wt + '_amount' ).val(),
                    'remark': $( wt + '_remark' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    wtm.toggle();
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    dt_table.draw( false );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( wt + '_' + key ).addClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).text( value );
                        } );
                    } else {
                        wtm.toggle();
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();
                    }
                }
            } );
        } );
    } );
</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>