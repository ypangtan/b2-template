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
        <div class="mb-3 text-end">
            @can( 'add roles' )
            <a class="btn btn-sm btn-success" href="{{ route( 'admin.role.add' ) }}">{{ __( 'template.create' ) }}</a>
            @endcan
        </div>
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

<x-toast/>

<script>

    window['columns'] = @json( $columns );
    window['ids'] = [];
    
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
                url: '{{ route( 'admin.role.allRoles' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'roles',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'name' },
                { data: 'guard_name' },
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

        var rc = '#{{ $role_create }}',
            re = '#{{ $role_edit }}',
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        $( document ).on( 'click', '.dt-edit', function() {
            window.location.href = '{{ route( 'admin.role.edit' ) }}?id=' + $( this ).data( 'id' );
        } );

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

<script src="{{ asset( 'admin/js/dataTable.init.js' ) . Helper::assetVersion() }}"></script>