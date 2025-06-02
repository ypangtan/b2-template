<?php
$multi_language = 'multi_language';
$columns = [
    [
        'type' => 'default',
        'id' => 'dt_no',
        'title' => 'No.',
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'audit.module_name' ) ] ),
        'id' => 'module',
        'title' => __( 'audit.module_name' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'multi_language.message_key' ) ] ),
        'id' => 'message_key',
        'title' => __( 'multi_language.message_key' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'multi_language.text' ) ] ),
        'id' => 'text',
        'title' => __( 'multi_language.text' ),
    ],
    [
        'type' => 'select',
        'options' => [
            [ 'value' => '', 'title' => __( 'datatables.all_x', [ 'title' => __( 'multi_language.language' ) ] ) ],
            [ 'value' => 10, 'title' => __( 'template.en' ) ],
            [ 'value' => 20, 'title' => __( 'template.zh' ) ],
        ],
        'id' => 'language',
        'title' => __( 'multi_language.language' ),
    ],
    [
        'type' => 'input',
        'placeholder' =>  __( 'datatables.search_x', [ 'title' => __( 'multi_language.last_update_by' ) ] ),
        'id' => 'last_update_by',
        'title' => __( 'multi_language.last_update_by' ),
    ],
    [
        'type' => 'default',
        'id' => 'dt_action',
        'title' => __( 'datatables.action' ),
    ]
];

?>

<div class="card">
    <div class="card-body">
        @if( auth()->user()->id == 1 )
        <div class="text-end mb-3">
            <a class="btn btn-sm btn-primary" href="{{ route( 'admin.multi_language.add' ) }}">{{ __( 'template.create' ) }}</a>
        </div>
        @endIf
        <x-data-tables id="multi_language_table" enableFilter="true" enableFooter="false" columns="{{ json_encode( $columns ) }}" />
    </div>
</div>

<div class="modal fade" id="modal_multi_language" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="mb-3">
                    <strong>{{ __( 'multi_language.multi_language_details' ) }}</strong>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $multi_language }}_module" class="col-sm-5 col-form-label">{{ __( 'multi_language.module' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" readonly class="form-control-plaintext" id="{{ $multi_language }}_module">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $multi_language }}_message_key" class="col-sm-5 col-form-label">{{ __( 'multi_language.message_key' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" readonly class="form-control-plaintext" id="{{ $multi_language }}_message_key">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $multi_language }}_language" class="col-sm-5 col-form-label">{{ __( 'multi_language.language' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" readonly class="form-control-plaintext" id="{{ $multi_language }}_language">
                    </div>
                </div>
                
                <hr>
                <div class="mb-3">
                    <strong>{{ __( 'datatables.action' ) }}</strong>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $multi_language }}_text" class="col-sm-5 col-form-label">{{ __( 'multi_language.text' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control" id="{{ $multi_language }}_text" row="7"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <input type="hidden" id="{{ $multi_language }}_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __( 'template.cancel' ) }}</button>
                <button type="button" class="btn btn-sm btn-primary btn_save">{{ __( 'template.save_changes' ) }}</button>
            </div>
        </div>
    </div>
</div>

<script>

    window['columns'] = @json( $columns );
    window['ids'] = [];
        
    @foreach ( $columns as $column )
    @if ( $column['type'] != 'default' )
    window['{{ $column['id'] }}'] = '';
    @endif
    @endforeach

    var dt_table,
        dt_table_name = '#multi_language_table',
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
                url: '{{ route( 'admin.multi_language.allMultiLanguages' ) }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                dataSrc: 'multi_languages',
            },
            lengthMenu: [
                [ 10, 25, 50, 999999 ],
                [ 10, 25, 50, '{{ __( 'datatables.all' ) }}' ]
            ],
            order: [[ 1, 'desc' ]],
            columns: [
                { data: null },
                { data: 'module' },
                { data: 'message_key' },
                { data: 'text' },
                { data: 'language' },
                { data: 'last_update_by' },
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
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "module" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "message_key" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "text" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "language" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ Helper::columnIndex( $columns, "last_update_by" ) }}' ),
                    orderable: false,
                    render: function( data, type, row, meta ) {
                        return data?.username ?? '-';
                    },
                },
                {
                    targets: parseInt( '{{ count( $columns ) - 1 }}' ),
                    orderable: false,
                    width: '10%',
                    className: 'text-center',
                    render: function( data, type, row, meta ) {

                        @canany( [ 'edit multi_languages' ] )

                        let view = '',
                            edit = '',
                            status = '';
                            detail = '';

                        @can( 'view multi_languages' )
                        view += '<li class="dropdown-item click-action dt-view" data-id="' + data + '">{{ __( 'template.view' ) }}</li>';
                        @endcan

                        let html = 
                        `
                        <div class="dropdown">
                            <i class="text-primary click-action" icon-name="more-horizontal" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                            ` + view + `
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
        let ml = '#{{ $multi_language }}';
            modalMultiLanguage = new bootstrap.Modal( document.getElementById( 'modal_multi_language' ) );

        $( document ).on( 'click', '.dt-view', function() {

            let id = $( this ).data( 'id' );

            $.ajax( {
                url: '{{ route( 'admin.multi_language.oneMultiLanguage' ) }}',
                type: 'POST',
                data: {
                    id,
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {

                    $( ml + '_module' ).val( response.module );
                    $( ml + '_message_key' ).val( response.message_key );
                    $( ml + '_language' ).val( response.language );
                    $( ml + '_text' ).val( response.text );
                    $( ml + '_id' ).val( response.encrypted_id );

                    modalMultiLanguage.show();
                },
                error: function( error ) {
                    $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                    modalDanger.show();
                },
            } );
        } );
        
        $( document ).on( 'click', '#modal_multi_language .btn_save', function() {
            $.ajax( {
                url: '{{ route( 'admin.multi_language.updateMultiLanguageAdmin' ) }}',
                type: 'POST',
                data: {
                    id: $( ml + '_id' ).val(),
                    text: $( ml + '_text' ).val(),
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    modalMultiLanguage.hide();
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.show();
                    dt_table.draw( false );
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ml + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                },
            } );
        } );

        $( '#submission_date' ).flatpickr( {
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