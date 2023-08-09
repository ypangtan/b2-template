<?php
$administrator_create = 'administrator_create';
$administrator_edit = 'administrator_edit';

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
            @can( 'add administrators' )
            <a class="btn btn-sm btn-success" href="{{ route( 'admin.administrator.add' ) }}">{{ __( 'template.create' ) }}</a>
            @endcan
        </div>
        <x-data-tables id="administrator_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<?php
array_shift( $data['roles'] );
$contents = [
    [
        'id' => '_username',
        'title' => __( 'administrator.username' ),
        'placeholder' => __( 'administrator.username' ),
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_email',
        'title' => __( 'administrator.email' ),
        'placeholder' => __( 'administrator.email' ),
        'type' => 'text',
        'mandatory' => true,
    ],
    [
        'id' => '_role',
        'title' => __( 'administrator.role' ),
        'placeholder' => __( 'administrator.role' ),
        'type' => 'select',
        'options' => $data['roles'],
        'mandatory' => true,
    ],
    [
        'id' => '_password',
        'title' => __( 'administrator.password' ),
        'placeholder' => __( 'administrator.password' ),
        'type' => 'password',
        'autocomplete' => 'new-password',
        'mandatory' => true,
    ],
];
?>

<script>

    window['columns'] = @json( $columns );
    window['ids'] = [];
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach
    
    var roles = @json( $data['roles'] ),
        dt_table,
        dt_table_name = '#administrator_table',
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
                url: '{{ route( 'admin.administrator.allAdministrators' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'administrators',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'username' },
                { data: 'email' },
                { data: 'role.name' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "role" ) }}' ),
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
                        @can( 'edit administrators' )
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
       
       $( document ).on( 'click', '.dt-edit', function() {
           window.location.href = '{{ route( 'admin.administrator.edit' ) }}?id=' + $( this ).data( 'id' );
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