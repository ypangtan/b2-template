<?php
$module_create = 'module_create';
$module_edit = 'module_edit';
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
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'module.module_name' ) ] ),
        'id' => 'module_name',
        'title' => __( 'module.module_name' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'module.guard_name' ) ] ),
        'id' => 'guard_name',
        'title' => __( 'module.guard_name' ),
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
        <div class="mb-3 text-end">
            @can( 'add admins' )
            <button class="btn btn-sm btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#{{ $module_create }}_canvas" aria-controls="{{ $module_create }}_canvas">{{ __( 'template.create' ) }}</button>
            @endcan
        </div>
        <x-data-tables id="module_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
$contents = [
    [
        'id' => '_module_name',
        'title' => __( 'module.module_name' ),
        'placeholder' => __( 'module.module_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_guard_name',
        'title' => __( 'module.guard_name' ),
        'placeholder' => __( 'module.guard_name' ),
        'type' => 'text',
        'mandatory' => true,
    ],
];
?>

<x-off-canvas.off-canvas title="{{ __( 'module.' . $module_create ) }}" crud="{{ $module_create }}" contents="{{ json_encode( $contents ) }}" />

<x-toast/>

<script>

    window['columns'] = @json( $columns );
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach
    
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
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {
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
                    'module_name': $( mc + '_module_name' ).val().trim().replace(/ /g,"_").toLowerCase(),
                    'guard_name': $( mc + '_guard_name' ).val().trim().toLowerCase(),
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

        window['createdDate'] = $( '#created_date' ).flatpickr( {
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