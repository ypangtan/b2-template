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
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'datatables.title' ) ] ),
        'id' => 'title',
        'title' => __( 'datatables.title' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [ 'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.type' ) ] ), 'value' => '' ],
            [ 'title' => __( 'announcement.news' ), 'value' => 2 ],
            [ 'title' => __( 'announcement.event' ), 'value' => 3 ],
        ],
        'id' => 'type',
        'title' => __( 'datatables.type' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [ 'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.status' ) ] ), 'value' => '' ],
            [ 'title' => __( 'datatables.published' ), 'value' => 10 ],
            [ 'title' => __( 'datatables.unpublished' ), 'value' => 20 ],
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
            @can( 'add announcements' )
            <button id="add" class="btn btn-sm btn-success" type="button">{{ __( 'template.create' ) }}</button>
            @endcan
        </div>
        <x-data-tables id="announcement_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<x-toast/>

<script>
    window['columns'] = @json( $columns );
    window['ids'] = [];
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var dt_table,
        dt_table_name = '#announcement_table',
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
                url: '{{ route( 'admin.announcement.allAnnouncements' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'notifications',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "title" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "type" ) }}' ),
                    render: function( data, type, row, meta ) {
                        return data == 2 ? '{{ __( 'announcement.news' ) }}' : '{{ __( 'announcement.event' ) }}';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "status" ) }}' ),
                    render: function( data, type, row, meta ) {
                        return data == 20 ? '{{ __( 'datatables.unpublished' ) }}' : '{{ __( 'datatables.published' ) }}';
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {

                        @canany( [ 'edit announcements', 'view announcements'. 'approve announcements' ] )

                        let view = '',
                            edit = '',
                            status = '';

                        @can( 'edit announcements' )
                        view += '<li class="dropdown-item click-action dt-edit" data-id="' + data + '">{{ __( 'datatables.edit' ) }}</li>';
                        status = row['status'] == 10 ? 
                        '<li class="dropdown-item click-action dt-status" data-id="' + data + '" data-status="20">{{ __( 'datatables.unpublish' ) }}</li>':
                        '<li class="dropdown-item click-action dt-status" data-id="' + data + '" data-status="10">{{ __( 'datatables.publish' ) }}</li>' ;
                        @endcan

                        let html = 
                        `
                        <div class="dropdown">
                            <i class="text-primary click-action" icon-name="more-horizontal" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                            ` + view + `
                            ` + status + `
                            </ul>
                        </div>
                        `;
                        return html;
                        @else
                        return '<i class="text-secondary" icon-name="more-horizontal" data-bs-toggle="dropdown"></i>';
                        @endcanany
                    },
                },
            ],
        },
        table_no = 0,
        timeout = null;

    document.addEventListener( 'DOMContentLoaded', function() {

        let toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        $( document ).on( 'click', '#add', function() {

            window.open( '{{ route( 'admin.announcement.add' ) }}', '_blank' );
        } );
        
        $( document ).on( 'click', '.dt-edit', function() {

            let id = $( this ).data( 'id' );

            window.open( '{{ route( 'admin.announcement.edit' ) }}/' + id, '_blank' );
        } );

         $( document ).on( 'click', '.dt-status', function() {

            let data = {
                id: $( this ).data( 'id' ),
                status: $( this ).data( 'status' ),
                _token: '{{ csrf_token() }}',
            }

            $.ajax( {
                url: '{{ route( 'admin.announcement.updateAnnouncementStatus' ) }}',
                type: 'POST',
                data: data,
                success: function( response ) {
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();
                    dt_table.draw( false );
                },
            } );
        } );

        $( '#created_date' ).flatpickr( {
            mode: 'range',
            disableMobile: true,
            onClose: function( selected, dateStr, instance ) {
                if ( $( instance.element ).val() ) {
                    window[$( instance.element ).data('id')] = $( instance.element ).val();
                    dt_table.draw( false );
                }
            }
        } );
    } );
</script>

<script src="{{ asset( 'admin/js/dataTable.init.js' ) . Helper::assetVersion() }}"></script>