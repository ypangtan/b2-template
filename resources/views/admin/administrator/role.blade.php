<?php
$role_create = 'role_create';
$role_edit = 'role_edit';

$multiSelect = 0;
?>

<?php
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
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'role.role_name' ) ] ),
        'id' => 'role_name',
        'title' => __( 'role.role_name' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'role.guard_name' ) ] ),
        'id' => 'guard_name',
        'title' => __( 'role.guard_name' ),
    ],
    [
        'type' => 'default',
        'id' => 'dt_action',
        'title' => __( 'datatables.action' ),
    ],
]
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="role_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
$contents = [
    [
        'id' => '_role_name',
        'title' => __( 'role.role_name' ),
        'placeholder' => __( 'role.role_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_guard_name',
        'title' => __( 'role.guard_name' ),
        'placeholder' => __( 'role.guard_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
];
?>

<x-off-canvas-role.off-canvas-role title="{{ __( 'role.' . $role_create ) }}" crud="{{ $role_create }}" contents="{{ json_encode( $contents ) }}" />

<x-off-canvas-role.off-canvas-role title="{{ __( 'role.' . $role_edit ) }}" crud="{{ $role_edit }}" contents="{{ json_encode( $contents ) }}" />

<x-toast/>

<script>

    window['columns'] = @json( $columns );
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach
    
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
                url: '{{ route( 'admin.administrator.allRoles' ) }}',
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
                { data: 'guard_name' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "role_name" ) }}' ),
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
                        @can( 'edit roles' )
                        return '<strong class="dt-edit table-action link-primary" data-id="' + data + '">Edit</strong>';
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

        $( document ).on( 'click', '.dt-edit', function() {

            var id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.administrator.oneRole' ) }}',
                type: 'POST',
                data: { id, '_token': '{{ csrf_token() }}', },
                success: function( response ) {
                    
                    $( re + '_id' ).val( response.role.id );
                    $( re + '_role_name' ).val( response.role.name );
                    $( re + '_guard_name' ).val( response.role.guard_name );

                    response.permissions.map( function( v, i ) {
                        $( re + '_' + v.name.replace(/ /g,"_") + '_' + v.guard_name ).prop( 'checked', true );
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
                url: '{{ route( 'admin.administrator.createRole' ) }}',
                type: 'POST',
                data: {
                    'role_name': $( rc + '_role_name' ).val().trim().replace(/ /g,"_").toLowerCase(),
                    'guard_name': $( rc + '_guard_name' ).val().trim().toLowerCase(),
                    modules,
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'role.role_created' ) }}' ); 
                    toast.show();
                    role_create_canvas.hide();
                    dt_table.draw( false );
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
                url: '{{ route( 'admin.administrator.updateRole' ) }}',
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
                    dt_table.draw( false );
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

        window['createdDate'] = $( '#created_date' ).flatpickr( {
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