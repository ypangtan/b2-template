<?php
$admin_create = 'admin_create';
$admin_edit = 'admin_edit';

$multiSelect = 0;
?>

<?php
array_unshift( $data['roles'],[ 'title' => __( 'datatables.all_x', [ 'title' => __( 'administrator.role' ) ] ), 'value' => '' ] );
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
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'administrator.username' ) ] ),
        'id' => 'username',
        'title' => __( 'administrator.username' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'administrator.email' ) ] ),
        'id' => 'email',
        'title' => __( 'administrator.email' ),
    ],
    [
        'type' => 'select',
        'options' => $data['roles'],
        'id' => 'role',
        'title' => __( 'administrator.role' ),
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
        <div class="mb-3 text-end">
            @can( 'add admins' )
            <button class="btn btn-sm btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{ $admin_create }}_canvas" aria-controls="{{ $admin_create }}_canvas">{{ __( 'template.create' ) }}</button>
            @endcan
        </div>
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

    window['columns'] = @json( $columns );
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach
    
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
                url: '{{ route( 'admin.administrator.allAdmins' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'admins',
            },
            lengthMenu: [[10, 1],[10, 1]],
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "dt_no" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return table_no += 1;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "role" ) }}' ),
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

        $( document ).on( 'click', '.dt-edit', function() {

            var id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.administrator.oneAdmin' ) }}',
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
                url: '{{ route( 'admin.administrator.createAdmin' ) }}',
                type: 'POST',
                data: {
                    'username': $( ac + '_username' ).val(),
                    'email': $( ac + '_email' ).val(),
                    'role': $( ac + '_role' ).val(),
                    'password': $( ac + '_password' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'administrator.admin_created' ) }}' ); 
                    toast.show();
                    admin_create_canvas.hide();
                    dt_table.draw( false );
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
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
                url: '{{ route( 'admin.administrator.updateAdmin' ) }}',
                type: 'POST',
                data: {
                    'id': $( ae + '_id' ).val(),
                    'username': $( ae + '_username' ).val(),
                    'email': $( ae + '_email' ).val(),
                    'role': $( ae + '_role' ).val(),
                    'password': $( ae + '_password' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'administrator.admin_updated' ) }}' );
                    toast.show();
                    admin_edit_canvas.hide();
                    dt_table.draw( false );
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ae + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );
        } );

        window['registeredDate'] = $( '#registered_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
            onClose: function( selected, dateStr, instance ) {
                window[$( instance.element ).data('id')] = $( instance.element ).val();
                dt_table.draw( false );
            }
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>