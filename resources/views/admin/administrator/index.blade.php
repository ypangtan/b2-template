<?php
$admin_create = 'admin_create';
$admin_edit = 'admin_edit';
?>

<div class="listing-header">
    <h1 class="h2 mb-3">{{ __( 'administrator.administrator' ) }}</h1>
    @can( 'add admins' )
    <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{ $admin_create }}_canvas" aria-controls="{{ $admin_create }}_canvas">{{ __( 'template.create' ) }}</button>
    @endcan
</div>

<?php
array_unshift( $data['roles'],[ 'title' => __( 'administrator.all' ), 'value' => '' ] );
$columns = [
    [
        'type' => 'default',
        'title' => 'No.',
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'datatables.search_x', [ 'title' => __( 'datatables.registered_date' ) ] ),
        'id' => 'search_date',
        'title' => __( 'datatables.registered_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'administrator.username' ) ] ),
        'title' => __( 'administrator.username' ),
        // 'preAmount' => true,
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'administrator.email' ) ] ),
        'title' => __( 'administrator.email' ),
        // 'amount' => true,
    ],
    [
        'type' => 'select',
        'options' => $data['roles'],
        'title' => __( 'administrator.role' ),
    ],
    [
        'type' => 'default',
        'title' => __( 'datatables.action' ),
    ],
];
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="admin_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
array_shift( $data['roles'] );
$contents = [
    [
        'id' => '_username',
        'title' => __( 'administrator.username' ),
        'placeholder' => 'Admin Username',
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_email',
        'title' => __( 'administrator.email' ),
        'placeholder' => 'Admin Email',
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_role',
        'title' => __( 'administrator.role' ),
        'placeholder' => 'Admin Role',
        'type' => 'select',
        'options' => $data['roles'],
        'mandatory' => true,
    ],
    [
        'id' => '_password',
        'title' => __( 'administrator.password' ),
        'placeholder' => 'Admin Password',
        'type' => 'password',
        'autocomplete' => 'new-password',
        'mandatory' => true,
    ],
];
?>

<x-off-canvas.off-canvas title="{{ __( 'administrator.' . $admin_create ) }}" crud="{{ $admin_create }}" contents="{{ json_encode( $contents ) }}" />

<?php
$contents[3]['title'] = __( 'administrator.leave_blank' );
$contents[3]['mandatory'] = false;

array_push( $contents, [
    'id' => '_id',
    'title' => __( 'administrator.id' ),
    'placeholder' => 'Admin ID',
    'type' => 'hidden',
] );
?>

<x-off-canvas.off-canvas title="{{ __( 'administrator.' . $admin_edit ) }}" crud="{{ $admin_edit }}" contents="{{ json_encode( $contents ) }}" />

<x-toast/>

<script>
    
    var roles = @json( $data['roles'] ),
        dt_table,
        dt_table_name = '#admin_table',
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
                url: '{{ Helper::baseAdminUrl() }}/administrators/all_admins',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'admins',
            },
            lengthMenu: [[10, 2],[10, 2]],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'username' },
                { data: 'email' },
                { data: 'role_name' },
                { data: 'id' },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return table_no += 1;
                    },
                },
                {
                    targets: 4,
                    render: function( data, type, row, meta ) {                       
                        return roles[roles.map( function( e ) { return e.key; } ).indexOf( data )].title;
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {
                        @can( 'edit admins' )
                        return '<i class="dt-edit table-action" data-feather="edit-3" data-id="' + data + '"></i>';
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

        var ac = '#{{ $admin_create }}',
            ae = '#{{ $admin_edit }}',
            admin_create_canvas = new bootstrap.Offcanvas( document.getElementById( 'admin_create_canvas' ) ),
            admin_edit_canvas = new bootstrap.Offcanvas( document.getElementById( 'admin_edit_canvas' ) ),
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        document.getElementById( 'admin_create_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( '.offcanvas-body .form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );
            $( '.offcanvas-body .form-select' ).removeClass( 'is-invalid' ).val( '' );
        } );

        document.getElementById( 'admin_edit_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( '.offcanvas-body .form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );
            $( '.offcanvas-body .form-select' ).removeClass( 'is-invalid' ).val( '' );
        } );

        $( '#search_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
        } );

        $( document ).on( 'click', '.dt-edit', function() {

            var id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/one_admin',
                type: 'POST',
                data: { id, '_token': '{{ csrf_token() }}', },
                success: function( response ) {
                    
                    $( ae + '_id' ).val( response.id );
                    $( ae + '_username' ).val( response.username );
                    $( ae + '_email' ).val( response.email );
                    $( ae + '_role' ).val( response.role );

                    admin_edit_canvas.show();
                },
            } );
        } );

        $( ac + '_submit' ).click( function() {

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/create_admin',
                type: 'POST',
                data: {
                    'username': $( ac + '_username' ).val(),
                    'email': $( ac + '_email' ).val(),
                    'role': $( ac + '_role' ).val(),
                    'password': $( ac + '_password' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( 'New Admin Added.' ); 
                    toast.show();
                    admin_create_canvas.hide();
                    dt_table.draw();
                },
                error: function( error ) {
                    if( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ac + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );

        } );

        $( ae + '_submit' ).click( function() {

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/update_admin',
                type: 'POST',
                data: {
                    'id': $( ae + '_id' ).val(),
                    'username': $( ae + '_username' ).val(),
                    'email': $( ae + '_email' ).val(),
                    'role': $( ae + '_role' ).val(),
                    'passord': $( ae + '_password' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {

                    if( response.status == 'ok' ) {
                        $( '#toast .toast-body' ).text( '{{ __( 'administrator.admin_updated' ) }}' );     
                    } else {
                        $( '#toast .toast-body' ).text( '{{ __( 'administrator.no_changes' ) }}' ); 
                    }

                    toast.show();
                    admin_edit_canvas.hide();
                    dt_table.draw();
                },
                error: function( error ) {
                    if( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ae + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } )
            
        } );

        $( '.form-control' ).focus( function() {
            if( $( this ).hasClass( 'is-invalid' ) ) {
                $( this ).removeClass( 'is-invalid' ).next().text( '' );
            }
        } );
        $( '.form-select' ).focus( function() {
            if( $( this ).hasClass( 'is-invalid' ) ) {
                $( this ).removeClass( 'is-invalid' ).next().text( '' );
            }
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>