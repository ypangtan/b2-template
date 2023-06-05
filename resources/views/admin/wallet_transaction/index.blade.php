<?php
array_unshift( $data['wallet'], [ 'title' => __( 'datatables.all_x', [ 'title' => __( 'wallet.wallet' ) ] ), 'value' => '' ] );
array_unshift( $data['transaction_type'], [ 'title' => __( 'datatables.all_x', [ 'title' => __( 'wallet.transaction_type' ) ] ), 'value' => '' ] );
$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'datatables.search_x', [ 'title' => __( 'datatables.created_date' ) ] ),
        'id' => 'created_date',
        'title' => __( 'datatables.created_date' ),
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
        'type' => 'select',
        'options' => $data['transaction_type'],
        'id' => 'transaction_type',
        'title' => __( 'wallet.transaction_type' ),
    ],
    [
        'type' => 'default',
        'id' => 'remark',
        'title' => __( 'wallet.remark' ),
        'preAmount' => true,
    ],
    [
        'type' => 'default',
        'id' => 'amount',
        'title' => __( 'wallet.amount' ),
        'amount' => true,
    ],
];
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="transaction_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
        
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var walletMapper = @json( $data['wallet'] ),
        transactionMapper = @json( $data['transaction_type'] ),
        dt_table,
        dt_table_name = '#transaction_table',
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
                url: '{{ route( 'admin.wallet_transaction.allWalletTransactions' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'transactions',
            },
            lengthMenu: [[10, 25],[10, 25]],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'user.username' },
                { data: 'type' },
                { data: 'transaction_type' },
                { data: 'converted_remark' },
                { data: 'listing_amount' },
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
                        return walletMapper[walletMapper.map( function( e ) { return e.value; } ).indexOf( data )].title;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "transaction_type" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return transactionMapper[transactionMapper.map( function( e ) { return e.value; } ).indexOf( data )].title;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "remark" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "amount" ) }}' ),
                    orderable: false,
                    className: 'text-end',
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    document.addEventListener( 'DOMContentLoaded', function() {

        $( '#created_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
            onClose: function( selected, dateStr, instance ) {
                window[$( instance.element ).data('id')] = $( instance.element ).val();
                dt_table.draw();
            }
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>