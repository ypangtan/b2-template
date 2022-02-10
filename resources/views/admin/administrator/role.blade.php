<?php
$role_create = 'role_create';
$role_edit = 'role_edit';
?>

<div class="listing-header">
    <h1 class="h3 mb-3">{{ __( 'role.roles' ) }}</h1>
    @can( 'add admins' )
    <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{ $role_create }}_canvas" aria-controls="{{ $role_create }}_canvas">{{ __( 'role.create' ) }}</button>
    @endcan
</div>

<?php
$columns = [
    [
        'type' => 'default',
        'title' => 'No.',
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'role.search_x', [ 'title' => __( 'role.created_date' ) ] ),
        'id' => 'search_date',
        'title' => __( 'role.created_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'role.search_x', [ 'title' => __( 'role.role_name' ) ] ),
        'title' => __( 'role.role_name' ),
    ],
    [
        'type' => 'default',
        'title' => __( 'role.action' ),
    ],
]
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="role_table" enableFilter="true" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
$contents = [
    [
        'id' => '_name',
        'title' => __( 'role.role_name' ),
        'placeholder' => __( 'role.role_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
];
?>

<x-off-canvas-role.off-canvas-role title="{{ __( 'role.' . $role_create ) }}" crud="{{ $role_create }}" contents="{{ json_encode( $contents ) }}" />

<x-off-canvas-role.off-canvas-role title="{{ __( 'role.' . $role_edit ) }}" crud="{{ $role_edit }}" contents="{{ json_encode( $contents ) }}" />

<x-toast/>

<script>
    
    var roles = { super_admin: '{{ __( "role.super_admin" ) }}', admin: '{{ __( "role.admin" ) }}' },
        dt_table,
        dt_table_name = '#role_table',
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
                url: '{{ Helper::baseAdminUrl() }}/administrators/all_roles',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'roles',
            },
            lengthMenu: [[10, 2],[10, 2]],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'name' },
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
                    targets: 2,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: 3,
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

        var rc = '#{{ $role_create }}',
            re = '#{{ $role_edit }}',
            role_create_canvas = new bootstrap.Offcanvas( document.getElementById( 'role_create_canvas' ) ),
            role_edit_canvas = new bootstrap.Offcanvas( document.getElementById( 'role_edit_canvas' ) ),
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        document.getElementById( 'role_create_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( 'input.form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );

            $( rc + '_canvas' + ' .form-check-input' ).each( function() {
                $( this ).prop( 'checked', false );
            } );
        } );

        document.getElementById( 'role_edit_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( 'input.form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );

            $( re + '_canvas' + ' .form-check-input' ).each( function() {
                $( this ).prop( 'checked', false );
            } );
        } );

        $( '#search_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
        } );

        $( document ).on( 'click', '.dt-edit', function() {

            var id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/one_role',
                type: 'POST',
                data: { id, '_token': '{{ csrf_token() }}', },
                success: function( response ) {
                    
                    $( re + '_id' ).val( response.role.id );
                    $( re + '_name' ).val( response.role.name );

                    response.permissions.map( function( v, i ) {
                        $( re + '_' + v.name.replace(/ /g,"_") ).prop( 'checked', true );
                    } );

                    role_edit_canvas.show();
                },
            } );
        } );

        $( rc + '_submit' ).click( function() {

            var modules = {};

            $( '.role_create-modules-section' ).each( function() {

                var temp = [];

                $( this ).find( '.form-check-input' ).each( function() {
                    
                    if( $( this ).prop( 'checked' ) ) {
                        temp.push( $( this ).val() );
                    }

                } );

                modules[ $( this ).data( 'module' ) ] = temp

            } );

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/create_role',
                type: 'POST',
                data: {
                    'name': $( rc + '_name' ).val().trim().replace(/ /g,"_").toLowerCase(),
                    modules,
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'role.role_created' ) }}' ); 
                    toast.show();
                    role_create_canvas.hide();
                    dt_table.draw();
                },
                error: function( error ) {
                    if( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( rc + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );

        } );

        $( re + '_submit' ).click( function() {

            var modules = {};

            $( '.role_edit-modules-section' ).each( function() {

                var temp = [];

                $( this ).find( '.form-check-input' ).each( function() {
                    
                    if( $( this ).prop( 'checked' ) ) {
                        temp.push( $( this ).val() );
                    }

                } );

                modules[ $( this ).data( 'module' ) ] = temp

            } );

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/update_role',
                type: 'POST',
                data: {
                    'id': $( re + '_id' ).val(),
                    modules,
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'role.role_updated' ) }}' ); 
                    toast.show();
                    role_edit_canvas.hide();
                    dt_table.draw();
                },
                error: function( error ) {
                    if( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( re + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );
            
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>