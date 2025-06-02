<?php
$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'datatables.search_x', [ 'title' => __( 'datatables.registered_date' ) ] ),
        'id' => 'submission_date',
        'title' => __( 'datatables.registered_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'mail_action.user' ) ] ),
        'id' => 'user',
        'title' => __( 'mail_action.user' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'mail_action.subject' ) ] ),
        'id' => 'subject',
        'title' => __( 'mail_action.subject' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'mail_action.email' ) ] ),
        'id' => 'email',
        'title' => __( 'mail_action.email' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [ 'value' => '', 'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.status' ) ] ) ],
            [ 'value' => 10, 'title' => __( 'mail_action.success' ) ],
            [ 'value' => 20, 'title' => __( 'mail_action.fail' ) ],
        ],
        'id' => 'status',
        'title' => __( 'mail_action.status' ),
    ],
    [
        'type' => 'default',
        'id' => 'dt_action',
        'title' => __( 'datatables.action' ),
    ]
];

?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="mail_action_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<div class="modal fade" id="modal_mail">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body" id="mail_body">
            </div>
            <div class="modal-footer">
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __( 'mail_action.close' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
    window['ids'] = [];
        
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var 
        statusMapper = {
            '10': {
                'color': 'badge rounded-pill bg-success',
                'text': '{{ __( 'mail_action.success' ) }}'
            },
            '20': {
                'color': 'badge rounded-pill bg-danger',
                'text': '{{ __( 'mail_action.fail' ) }}'
            },
        },
        dt_table,
        dt_table_name = '#mail_action_table',
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
                url: '{{ route( 'admin.mail_action.allMailActions' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'mail_actions',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'user' },
                { data: 'subject' },
                { data: 'email' },
                { data: 'status' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "user" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {

                        let email = data?.email ?? '-';
                            fullname = data?.username ?? '-',
                            html = '';

                        html +=
                        `
                        <span>
                        <strong  class="name">` + fullname + `</strong><br>
                        <strong>{{ __( 'user.email' ) }}</strong>: ` + email + `<br>
                        </span>
                        `;

                        return html;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "subject" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "email" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "status" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {

                        if ( !data ) {
                            return '<span class="' + statusMapper[1].color + '">' + statusMapper[1].text + '</span>';
                        }

                        return '<span class="' + statusMapper[data].color + '">' + statusMapper[data].text + '</span>';
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {

                        @canany( [ 'edit mail_actions' ] )

                        let view = '',
                            edit = '',
                            status = '';
                            detail = '';

                        @can( 'edit mail_actions' )
                        edit += '<li class="dropdown-item click-action dt-resend" data-id="' + data + '">{{ __( 'mail_action.resend' ) }}</li>';
                        @endcan
                        @can( 'view mail_actions' )
                        view += '<li class="dropdown-item click-action dt-view" data-id="' + data + '">{{ __( 'mail_action.view_mail' ) }}</li>';
                        @endcan

                        let html = 
                        `
                        <div class="dropdown">
                            <i class="text-primary click-action" icon-name="more-horizontal" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                            ` + edit + `
                            ` + view + `
                            </ul>
                        </div>
                        `;
                        return html;
                        @else
                        return '<i class="text-secondary" icon-name="more-horizontal" data-bs-toggle="dropdown"></i>';
                        @endcanany
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    document.addEventListener( 'DOMContentLoaded', function() {
        let modalMail = new bootstrap.Modal( document.getElementById( 'modal_mail' ) );

        $( document ).on( 'click', '.dt-resend', function() {

            let id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.mail_action.resendMail' ) }}',
                type: 'POST',
                data: {
                    id,
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();
                    dt_table.draw( false );
                },
                error: function( error ) {
                    $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                    modalDanger.show();
                },
            } );
        } );
        
        $( document ).on( 'click', '.dt-view', function() {

            let id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.mail_action.oneMailAction' ) }}',
                type: 'POST',
                data: {
                    id,
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#mail_subject' ).html( response.subject );
                    $( '#mail_body' ).html( response.mail );

                    modalMail.show();

                },
                error: function( error ) {
                    $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                    modalDanger.show();
                },
            } );
        } );

        $( '#submission_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
            onClose: function( selected, dateStr, instance ) {
                window[$( instance.element ).data('id')] = $( instance.element ).val();
                dt_table.draw();
            }
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) . Helper::assetVersion() }}"></script>