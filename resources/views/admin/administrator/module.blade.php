<?php
$module_create = 'module_create';
$module_edit = 'module_edit';
?>

<div class="listing-header">
    <h1 class="h3 mb-3">{{ __( 'module.module' ) }}</h1>
    @can( 'add admins' )
    <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{ $module_create }}_canvas" aria-controls="{{ $module_create }}_canvas">{{ __( 'module.create' ) }}</button>
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
        'placeholder' => __( 'module.search_x', [ 'title' => __( 'module.created_date' ) ] ),
        'id' => 'search_date',
        'title' => __( 'module.created_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'module.search_x', [ 'title' => __( 'module.module_name' ) ] ),
        'title' => __( 'module.module_name' ),
    ],
    [
        'type' => 'default',
        'title' => __( 'module.action' ),
    ],
]
?>

<div class="card">
    <div class="card-body">
        <x-data-tables id="module_table" enableFilter="true" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
$contents = [
    [
        'id' => '_name',
        'title' => __( 'module.module_name' ),
        'placeholder' => __( 'module.module_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
];
?>

<x-off-canvas.off-canvas title="{{ __( 'module.' . $module_create ) }}" crud="{{ $module_create }}" contents="{{ json_encode( $contents ) }}" />

<x-toast/>

<script>
    
    var roles = { super_admin: '{{ __( "module.super_admin" ) }}', admin: '{{ __( "module.admin" ) }}' },
        dt_table,
        dt_table_name = '#module_table',
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
                url: '{{ Helper::baseAdminUrl() }}/administrators/all_modules',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'modules',
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
                    targets: 3,
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {
                        // return '<i class="dt-edit table-action" data-feather="edit-3" data-id="' + data + '"></i>';
                        return '-';
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    document.addEventListener( 'DOMContentLoaded', function() {

        var mc = '#{{ $module_create }}',
            me = '#{{ $module_edit }}',
            module_create_canvas = new bootstrap.Offcanvas( document.getElementById( 'module_create_canvas' ) ),
            // module_edit_canvas = new bootstrap.Offcanvas( document.getElementById( 'module_edit_canvas' ) ),
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        document.getElementById( 'module_create_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( 'input.form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );
        } );

        // document.getElementById( 'module_edit_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
        //     $( 'input.form-control' ).removeClass( 'is-invalid' ).val( '' );
        //     $( '.invalid-feedback' ).text( '' );
        // } );

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
                    
                    $( me + '_id' ).val( response.id );
                    $( me + '_username' ).val( response.username );
                    $( me + '_email' ).val( response.email );
                    $( me + '_role' ).val( response.role );

                    module_edit_canvas.show();
                },
            } );
        } );

        $( mc + '_submit' ).click( function() {

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/administrators/create_module',
                type: 'POST',
                data: {
                    'name': $( mc + '_name' ).val().trim().replace(/ /g,"_").toLowerCase(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#toast .toast-body' ).text( '{{ __( 'module.module_created' ) }}' ); 
                    toast.show();
                    module_create_canvas.hide();
                    dt_table.draw();
                },
                error: function( error ) {
                    if( error.status === 422 ) {
                        var errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( mc + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );

        } );

        $( me + '_submit' ).click( function() {


            
        } );
    } );

</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>