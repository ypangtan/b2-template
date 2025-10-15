<?php
$enableReorder = 1; // if table have priority attribute

$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'datatables.search_x', [ 'title' => __( 'datatables.registered_date' ) ] ),
        'id' => 'registered_date',
        'title' => __( 'datatables.registered_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'user.user' ) ] ),
        'id' => 'user',
        'title' => __( 'user.user' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'user.phone_number' ) ] ),
        'id' => 'phone_number',
        'title' => __( 'user.phone_number' ),
    ],
    [
        'type' => 'default',
        'id' => 'invitation_code',
        'title' => __( 'user.invitation_code' ),
    ],
    [
        'type' => 'default',
        'id' => 'capital',
        'title' => __( 'user.capital' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'user.referral' ) ] ),
        'id' => 'referral',
        'title' => __( 'user.referral' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [ 'value' => '', 'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.status' ) ] ) ],
            [ 'value' => 10, 'title' => __( 'datatables.activated' ) ],
            [ 'value' => 20, 'title' => __( 'datatables.suspended' ) ],
        ],
        'id' => 'status',
        'title' => __( 'datatables.status' ),
    ],
    [
        'type' => 'default',
        'id' => 'dt_action',
        'title' => __( 'datatables.action' ),
    ],
];

if ( $enableReorder == 1 ) {
    array_unshift( $columns,  [
        'type' => 'default',
        'id' => 'dt_reorder',
        'title' => '',
        'reorder' => 'yes',
    ] );
}
?>

<style>
    .dt-rowReorder-float-parent{
        background-color: white;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="mb-3 text-end">
            @can( 'add users' )
            <a class="btn btn-sm btn-primary" href="{{ route( 'admin.user.add' ) }}">{{ __( 'template.create' ) }}</a>
            @endcan
        </div>
        <x-data-tables id="user_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
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
    
    var statusMapper = {
            '10': {
                'text': '{{ __( 'datatables.activated' ) }}',
                'color': 'badge rounded-pill bg-success',
            },
            '20': {
                'text': '{{ __( 'datatables.suspended' ) }}',
                'color': 'badge rounded-pill bg-danger',
            },
        },
        dt_table,
        dt_table_name = '#user_table',
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
                url: '{{ route( 'admin.user.allUsers' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'users',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'email' },
                { data: 'phone_number' },
                { data: 'invitation_code' },
                { data: 'capital' },
                { data: 'referral' },
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

                        let email = data ?? '-';
                            fullname = row.user_detail?.fullname ?? '-',
                            html = '';

                        let img = row.user_detail.photo ? row.user_detail.photo_path : 'https://ui-avatars.com/api/?background=99b0ff&color=fff&name=' + fullname;

                        html +=
                        `
                        <div class="d-flex align-items-center">
                            <img class="user-img" src="` + img + `"></img>
                            <span class="ms-2">
                                <strong>` + fullname + `</strong><br>
                                <strong>{{ __( 'user.email' ) }}</strong>: ` + email + `
                            </span>
                        </div>
                        `;

                        return html;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "phone_number" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ? ( row.calling_code + data ) : '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "invitation_code" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "capital" ) }}' ),
                    orderable: false,
                    className: 'text-end',
                    render: function( data, type, row, meta ) {
                        return data ? data : '0.00';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "referral" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {

                        if ( !data ) {
                            return '-';
                        }

                        let email = data?.email ?? '-',
                            fullname = data.user_detail?.fullname ?? '-',
                            html = '';

                        html +=
                        `
                        <span>
                        <strong>` + fullname + `</strong><br>
                        <strong>{{ __( 'user.email' ) }}</strong>: ` + email + `
                        </span>
                        `;

                        return html;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "status" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return '<span class="' + statusMapper[data].color + '">' + statusMapper[data].text + '</span>';
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {

                        @canany( [ 'edit users', 'view users' ] )

                        let view = '',
                            edit = '',
                            status = '';

                        @can( 'edit users' )
                        view += '<li class="dropdown-item click-action dt-edit" data-id="' + data + '">{{ __( 'datatables.edit' ) }}</li>';
                        status = row.status == 10 ? 
                        '<li class="dropdown-item click-action dt-suspend" data-id="' + data + '">{{ __( 'datatables.suspend' ) }}</li>':
                        '<li class="dropdown-item click-action dt-activate" data-id="' + data + '">{{ __( 'datatables.activate' ) }}</li>' ;
                        @endcan

                        let html = 
                        `
                        <div class="dropdown">
                            <i class="text-primary click-action" data-lucide="more-horizontal" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                            ` + view + `
                            ` + status + `
                            </ul>
                        </div>
                        `;
                        return html;
                        @else
                        return '<i class="text-secondary" data-lucide="more-horizontal" data-bs-toggle="dropdown"></i>';
                        @endcanany
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    if ( parseInt( '{{ $enableReorder }}' ) == 1 ) {

        dt_table_config.rowReorder = {
            selector: '.dt-reorder',
            dataSrc: 'id',
            snapX: true,
            update: false,
        };

        dt_table_config.order[0] = [ 2, 'desc' ],
        dt_table_config.columns.unshift( {
            data: 'encrypted_id'
        } );
        dt_table_config.columnDefs.unshift( {
            targets: 0,
            orderable: false,
            className: 'reorder-handle',
            render: function( data, type, row, meta ) {
                return `<div class="dt-reorder"style="width: 100%" data-id="${data}" />
                    <i data-lucide="grip-vertical" class="align-middle" style="color: #5f5f5f;"></i>
                </div>`;
            },
        } );
    }

    document.addEventListener( 'DOMContentLoaded', function() {
       lucide.createIcons();
       
        $( document ).on( 'click', '.dt-edit', function() {
            window.location.href = '{{ route( 'admin.user.edit' ) }}?id=' + $( this ).data( 'id' );
        } );

        let uid = 0,
            status = '',
            scope = '';

        $( document ).on( 'click', '.dt-suspend', function() {

            uid = $( this ).data( 'id' );
            status = 20,
            scope = 'status';

            $( '#modal_confirmation_title' ).html( '{{ __( 'template.x_y', [ 'action' => __( 'datatables.suspend' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );
            $( '#modal_confirmation_description' ).html( '{{ __( 'template.are_you_sure_to_x_y', [ 'action' => __( 'datatables.suspend' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );

            modalConfirmation.show();
        } );

        $( document ).on( 'click', '.dt-activate', function() {

            uid = $( this ).data( 'id' );
            status = 10,
            scope = 'status';

            $( '#modal_confirmation_title' ).html( '{{ __( 'template.x_y', [ 'action' => __( 'datatables.activate' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );
            $( '#modal_confirmation_description' ).html( '{{ __( 'template.are_you_sure_to_x_y', [ 'action' => __( 'datatables.activate' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );

            modalConfirmation.show();
        } );

        $( document ).on( 'click', '#modal_confirmation_submit', function() {

            switch ( scope ) {
                case 'status':
                    $.ajax( {
                        url: '{{ route( 'admin.user.updateUserStatus' ) }}',
                        type: 'POST',
                        data: {
                            id: uid,
                            status,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function( response ) {
                            modalConfirmation.hide();
                            $( '#modal_success .caption-text' ).html( response.message );
                            modalSuccess.show();
                            dt_table.draw( false );
                        },
                        error: function( error ) {
                            modalConfirmation.hide();
                            $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                            modalDanger.show();
                        },
                    } );        
            }
        } );

        $( '#registered_date' ).flatpickr( {
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