<?php
$multiSelect = 0;
$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'default',
        'id' => 'thumbnail',
        'title' => __( 'datatables.thumbnail' ),
    ],
    [
        'type' => 'date',
        'placeholder' => __( 'datatables.search_x', [ 'title' => __( 'datatables.created_date' ) ] ),
        'id' => 'created_date',
        'title' => __( 'datatables.created_date' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'datatables.title' ) ] ),
        'id' => 'title',
        'title' => __( 'datatables.title' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [
                'title' => __( 'datatables.all_x', [ 'title' => __( 'category.category_type' ) ] ),
                'value' => ''
            ],
            [
                'title' => __( 'category.parent' ),
                'value' => '1'
            ],
            [
                'title' => __( 'category.child' ),
                'value' => '2'
            ]
        ],
        'id' => 'type',
        'title' => __( 'category.category_type' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [
                'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.status' ) ] ),
                'value' => ''
            ],
            [
                'title' => __( 'datatables.enabled' ),
                'value' => '10'
            ],
            [
                'title' => __( 'datatables.disabled' ),
                'value' => '1'
            ]
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
?>

<div class="card">
    <div class="card-body">
        <div class="mb-3 text-end">
            @can( 'add categories' )
            <a href="{{ route( 'admin.category.add' ) }}" class="btn btn-sm btn-success">{{ __( 'template.create' ) }}</a>
            @endcan
        </div>
        <x-data-tables id="category_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var statusMapper = {
            '1': '{{ __( 'datatables.disabled' ) }}',
            '10': '{{ __( 'datatables.enabled' ) }}',
        },
        typeMapper = {
            '1': '{{ __( 'category.parent' ) }}',
            '2': '{{ __( 'category.child' ) }}',
        },
        dt_table,
        dt_table_name = '#category_table',
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
                url: '{{ route( 'admin.category.allCategories' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'categories',
            },
            lengthMenu: [[10, 2],[10, 2]],
            order: [[ 2, 'desc' ]],
            columns: [
                { data: null },
                { data: 'path' },
                { data: 'created_at' },
                { data: 'title' },
                { data: 'type' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "thumbnail" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return '<img src="' + ( data ? data : '{{ asset( 'admin/img/placeholder/fff.jpg' ) }}' ) + '" width="75px" />';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "type" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return typeMapper[data];
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "status" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return statusMapper[data];
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {
                        @can( 'edit categories' )
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

        window['createdDate'] = $( '#created_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
            onClose: function( selected, dateStr, instance ) {
                window[$( instance.element ).data('id')] = $( instance.element ).val();
                dt_table.draw( false );
            }
        } );

        $( document ).on( 'click', '.dt-edit', function() {

            let id = $( this ).data( 'id' );

            window.location.href = '{{ route( 'admin.category.edit' ) }}?id=' + id;
        } );
    } );
</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) }}"></script>