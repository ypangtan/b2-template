<?php
$audit_view = 'audit_view';
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
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'audit.username' ) ] ),
        'id' => 'username',
        'title' => __( 'audit.username' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'audit.module_name' ) ] ),
        'id' => 'module_name',
        'title' => __( 'audit.module_name' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'audit.action_performed' ) ] ),
        'id' => 'action_performed',
        'title' => __( 'audit.action_performed' ),
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
        <x-data-tables id="audit_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<div class="offcanvas offcanvas-end offcanvas-right" tabindex="-1" id="audit_view_canvas" aria-labelledby="audit_view_canvas_label">
    <div class="offcanvas-header">
        <h2 id="audit_view_canvas_label">{{ __( 'audit.meta_data' ) }}</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3 row">
                            <label for="{{ $audit_view }}_ip_address" class="col-sm-4 col-form-label">{{ __( 'audit.ip_address' ) }}</label>
                            <div class="col-sm-8">
                                <input type="text" onfocus="this.blur()" class="form-control form-control-sm form-control-plaintext" id="{{ $audit_view }}_ip_address">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="{{ $audit_view }}_ip_address" class="col-sm-4 col-form-label">{{ __( 'audit.browser' ) }}</label>
                            <div class="col-sm-8">
                                <input type="text" onfocus="this.blur()" class="form-control form-control-sm form-control-plaintext" id="{{ $audit_view }}_browser">
                            </div>
                        </div>
                        <div class="row">
                            <label for="{{ $audit_view }}_ip_address" class="col-sm-4 col-form-label">{{ __( 'audit.operating_system' ) }}</label>
                            <div class="col-sm-8">
                                <input type="text" onfocus="this.blur()" class="form-control form-control-sm form-control-plaintext" id="{{ $audit_view }}_operating_system">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3 hidden" id="audit_view_canvas_old_section">
                            <strong style="font-size: 18px;">{{ __( 'audit.old_value' ) }}</strong>
                            <br>
                            <p class="ajax-data" id="audit_view_canvas_old" style="font-size: 16px;"></p>
                        </div>
                        <div class="mb-3" id="audit_view_canvas_new_section">
                            <strong style="font-size: 18px;">{{ __( 'audit.new_value' ) }}</strong>
                            <br>
                            <p class="ajax-data" id="audit_view_canvas_new" style="font-size: 16px;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="offcanvas-button-group">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas" aria-label="Close" id="offcanvas_close">{{ __( 'template.cancel' ) }}</button>
        </div>
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
    
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach
    
    var dt_table,
        dt_table_name = '#audit_table',
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
                url: '{{ route( 'admin.audit.allAudits' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'logs',
            },
            lengthMenu: [[10, 2],[10, 2]],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'created_at' },
                { data: 'admin_username' },
                { data: 'log_name' },
                { data: 'description' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "username" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "module_name" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data;
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "action_performed" ) }}' ),
                    orderable: false,
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
                        @can( 'edit audits' )
                        return '<strong class="dt-edit table-action link-primary" data-id="' + data + '">{{ __( 'datatables.view' ) }}</strong>';
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

        var av = '#{{ $audit_view }}',
            audit_view_canvas = new bootstrap.Offcanvas( document.getElementById( 'audit_view_canvas' ) ),
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        document.getElementById( 'audit_view_canvas' ).addEventListener( 'hidden.bs.offcanvas', function() {
            $( '.offcanvas-body .form-control' ).removeClass( 'is-invalid' ).val( '' );
            $( '.invalid-feedback' ).text( '' );
            $( '.offcanvas-body .form-select' ).removeClass( 'is-invalid' ).val( '' );
        } );

        $( document ).on( 'click', '.dt-edit', function() {

            var id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.audit.oneAudit' ) }}',
                type: 'POST',
                data: { id, '_token': '{{ csrf_token() }}', },
                success: function( response ) {

                    let properties = JSON.parse( response.properties );
                    $( av + '_ip_address' ).val( properties.agent.ip );
                    $( av + '_browser' ).val( properties.agent.browserName );
                    $( av + '_operating_system' ).val( properties.agent.os );

                    if( properties.old != undefined ) {
                        $( av + '_canvas_old_section' ).removeClass( 'hidden' );
                        var html1 = '';
                        for( var key of Object.keys( properties.old ) ) {
                            html1 += `<p><strong>`+key+ `</strong>: ` + properties.old[key] +`</p>`;
                        }
                        $( av + '_canvas_old' ).html( html1 );
                    } else {
                        $( av + '_canvas_old_section' ).addClass( 'hidden' );
                    }

                    var html2 = '';
                    console.log( properties );
                    if( properties != undefined && properties.length != 0 ) {
                        for( var key of Object.keys( properties.attributes ) ) {
                            html2 += `<p><strong>`+key+ `</strong>: ` + properties.attributes[key] +`</p>`;
                        }
                        $( av + '_canvas_new' ).html( html2 );
                    }
                    console.log(html2);

                    audit_view_canvas.show();
                },
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