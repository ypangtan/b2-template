<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$modelClass = $data['model'] ?? \App\Models\User::class;
$model = new $modelClass();
$table = $model->getTable();
$columns = $modelClass::$showAttribute;
$select2Fields = [];

// 判断是否启用 reorder
$enableReorder = in_array('priority', $columns) ? 1 : 2;

$autoColumns = [];
$autoColumns[] = [
    'type' => 'default',
    'id' => 'dt_no',
    'title' => 'No.',
];

foreach ($columns as $col) {
    $colType = DB::getSchemaBuilder()->getColumnType($table, $col);

    if ( Str::endsWith( $col, '_id' ) ) {
        $select2Fields[] = $col;
        $autoColumns[] = [
            'type' => 'select2',
            'id' => $col,
            'title' => __( $table . '.' . Str::singular( Str::beforeLast( $col, '_id' ) ) ),
            'placeholder' => __("datatables.search_x", ['title' => __("datatables.$col")]),
        ];
    } elseif (in_array($col, ['status', 'type'])) {
        $options = $data[$col];
        array_unshift( $options, [ 'value' => '', 'title' => __( 'datatables.all_x', [ 'title' => __( 'datatables.status' ) ] ) ] );
        $autoColumns[] = [
            'type' => 'select',
            'id' => $col,
            'title' => __( "$table.$col"),
            'options' => $options,
        ];
    } elseif (Str::contains($col, ['date', 'time', 'created_at', 'updated_at'])) {
        $autoColumns[] = [
            'type' => 'date',
            'id' => $col,
            'title' => __( "$table.$col"),
            'placeholder' => __("datatables.search_x", ['title' => __("datatables.$col")]),
        ];
    } else {
        $autoColumns[] = [
            'type' => 'default',
            'id' => $col,
            'title' => __( "$table.$col"),
            'placeholder' => __("datatables.search_x", ['title' => __("datatables.$col")]),
        ];
    }
}

if ( $enableReorder == 1 ) {
    array_unshift($autoColumns, [
        'type' => 'default',
        'id' => 'dt_reorder',
        'title' => '',
        'reorder' => 'yes',
    ]);
}

$autoColumns[] = [
    'type' => 'default',
    'id' => 'dt_action',
    'title' => __('datatables.action'),
];

$routeName = 'admin.' . Str::singular($table) . '.all' . Str::studly($table);

?>

<div class="card">
    <div class="card-body">
        <div class="mb-3 text-end">
            @can("add {$table}")
            <a class="btn btn-sm btn-primary" href="{{ route('admin.' . Str::singular( $table ) . '.add') }}">
                {{ __('template.create') }}
            </a>
            @endcan
        </div>

        <x-data-tables id="{{ $table }}_table" enableFilter="true" enableFooter="false" columns="{{ json_encode($autoColumns) }}"/>
    </div>
</div>

<script>
    window['columns'] = @json( $autoColumns );
    window['ids'] = [];

    @foreach ( $autoColumns as $v )
    @if ( $v['type'] != 'default' )
    window['{{ $v['id'] }}'] = '';
    @endif
    @endforeach

var dt_table,
    dt_table_name = '#{{ $table }}_table',
    table_no = 0,
    timeout = null,
    statusMapper = @json( $data['status'] ),
    dt_table_config = {
        language: {
            lengthMenu: '{{ __("datatables.lengthMenu") }}',
            zeroRecords: '{{ __("datatables.zeroRecords") }}',
            info: '{{ __("datatables.info") }}',
            infoEmpty: '{{ __("datatables.infoEmpty") }}',
            infoFiltered: '{{ __("datatables.infoFiltered") }}',
            paginate: {
                previous: '{{ __("datatables.previous") }}',
                next: '{{ __("datatables.next") }}',
            },
        },
        ajax: {
            url: '{{ route( $routeName ) }}',
            data: { _token: '{{ csrf_token() }}' },
            dataSrc: '{{ $table }}',
        },
        lengthMenu: [
            [10, 25, 50, 999999],
            [10, 25, 50, '{{ __("datatables.all") }}'],
        ],
        order: [[ '{{ Helper::columnIndex( $autoColumns, "created_at" ) }}', 'desc']],
        columns: [
            { data: null },
            @foreach ($columns as $col)
            { data: '{{ $col }}' },
            @endforeach
            { data: 'encrypted_id' },
        ],
        columnDefs: [
            {
                targets: parseInt( '{{ Helper::columnIndex( $autoColumns, "dt_no" ) }}' ),
                orderable: false,
                render: function( data, type, row, meta ) {
                    return table_no += 1;
                },
            },
            {
                targets: parseInt( '{{ Helper::columnIndex( $autoColumns, "dt_action" ) }}' ),
                orderable: false,
                className: 'text-center',
                render: function( data, type, row, meta ) {

                        @canany( [ 'edit {$table}', 'view {$table}' ] )

                        let view = '',
                            edit = '',
                            status = '';

                        @can( 'edit {$table}' )
                        view += '<li class="dropdown-item click-action dt-edit" data-id="' + data + '">{{ __( 'datatables.edit' ) }}</li>';
                        status = row.status == 10 ? 
                        '<li class="dropdown-item click-action dt-suspend" data-id="' + data + '">{{ __( 'datatables.suspend' ) }}</li>':
                        '<li class="dropdown-item click-action dt-activate" data-id="' + data + '">{{ __( 'datatables.activate' ) }}</li>' ;
                        @endcan

                        let html = 
                        `
                        <div class="dropdown">
                            <i class="text-primary click-action" data-lucide="more-horizontal" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                            ` + view + `
                            ` + status + `
                            </ul>
                        </div>
                        `;
                        return html;
                        @else
                        return '<i class="text-secondary" data-lucide="more-horizontal" data-bs-toggle="dropdown"></i>';
                        @endcanany
                    },
            },
            @foreach ( $columns as $col )
            {
                targets: parseInt( '{{ Helper::columnIndex( $autoColumns, $col ) }}' ),
                orderable: {{ $col === 'created_at' ? 'true' : 'false' }},
                render: function( data, type, row, meta ) {
                    @if ($col === 'status')
                        if (!data || !statusMapper[data]) return '-';
                        let s = statusMapper[data];
                        return `<span class="${s.color}">${s.title}</span>`;
                    @elseif ( Str::endsWith( $col, '_id') )
                        v = row['{{ Str::singular( getTableName( $col, $table )  ) }}'];
                        return v ? v.name || v.country_name || v.username || v.title : '-';
                    @else
                        return data ?? '-';
                    @endif
                },
            },
            @endforeach
        ],
    };

if ( parseInt( '{{ $enableReorder }}' ) == 1 ) {

    dt_table_config.rowReorder = {
        selector: '.dt-reorder',
        dataSrc: 'id',
        snapX: true,
        update: false,
    };

    dt_table_config.columns.unshift( {
        data: 'encrypted_id'
    } );
    dt_table_config.columnDefs.unshift( {
        targets: 0,
        orderable: false,
        className: 'reorder-handle',
        render: function( data, type, row, meta ) {
            return `<div class="dt-reorder"style="width: 100%" data-id="${data}" />
                <i data-lucide="grip-vertical" class="align-middle" style="color: #5f5f5f;"></i>
            </div>`;
        },
    } );
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    $(document).on('click', '.dt-edit', function() {
        let id = $(this).data('id');
        window.location.href = '{{ route("admin." . Str::singular( $table ) . ".edit") }}?id=' + id;
    });
    
    let uid = 0,
        status = '',
        scope = '';

    $( document ).on( 'click', '.dt-suspend', function() {

        uid = $( this ).data( 'id' );
        status = 20,
        scope = 'status';

        $( '#modal_confirmation_title' ).html( '{{ __( 'template.x_y', [ 'action' => __( 'datatables.suspend' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );
        $( '#modal_confirmation_description' ).html( '{{ __( 'template.are_you_sure_to_x_y', [ 'action' => __( 'datatables.suspend' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );

        modalConfirmation.show();
    } );

    $( document ).on( 'click', '.dt-activate', function() {

        uid = $( this ).data( 'id' );
        status = 10,
        scope = 'status';

        $( '#modal_confirmation_title' ).html( '{{ __( 'template.x_y', [ 'action' => __( 'datatables.activate' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );
        $( '#modal_confirmation_description' ).html( '{{ __( 'template.are_you_sure_to_x_y', [ 'action' => __( 'datatables.activate' ), 'title' => Str::singular( __( 'template.users' ) ) ] ) }}' );

        modalConfirmation.show();
    } );

    $( document ).on( 'click', '#modal_confirmation_submit', function() {

        switch ( scope ) {
            case 'status':
                let updateStatusRoute = '{{ route("admin." . Str::singular($table) . ".update" . Str::studly( Str::singular($table) ) . "Status") }}';
                $.ajax( {
                    url: updateStatusRoute,
                    type: 'POST',
                    data: {
                        id: uid,
                        status,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function( response ) {
                        modalConfirmation.hide();
                        $( '#modal_success .caption-text' ).html( response.message );
                        modalSuccess.show();
                        dt_table.draw( false );
                    },
                    error: function( error ) {
                        modalConfirmation.hide();
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.show();
                    },
                } );        
        }
    } );

    @foreach( $select2Fields as $field )
        @php
        $routeName = 'admin.' . Str::singular( getTableName( $field, $table )  ) . '.all' . Str::studly( getTableName( $field, $table )  );
        @endphp
        $('#{{ $field }}').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '{{ __("datatables.search_x", ["title" => __("datatables.$field")]) }}',
            allowClear: true,
            ajax: { 
                url: '{{ route( $routeName) }}',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        start: ((params.page ? params.page : 1) - 1) * 10,
                        length: 10,
                        _token: '{{ csrf_token() }}',
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    let processed = [];
                    let tableName = '{{ getTableName( $field, $table ) }}';
                    let list = data[tableName] || data.data || data.results || [];

                    if (!Array.isArray(list)) list = [];

                    list.forEach(v => {
                        processed.push({
                            id: v.encrypted_id || v.id,
                            text: v.name || v.country_name || v.username || v.title,
                            image: v.country_image ?? ''
                        });
                    });

                    return {
                        results: processed,
                        pagination: {
                            more: (params.page * 10) < (data.recordsFiltered || data.total || 0)
                        }
                    };
                },
                cache: true
            },
            templateResult: function (data) {
                if (!data.id) return data.text;
                let img = data.image ? `<img src="${data.image}" style="width:20px; height:14px; margin-right:8px;">` : '';
                return $(`<span>${img}${data.text}</span>`);
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                let img = data.image ? `<img src="${data.image}" style="width:20px; height:14px; margin-right:8px;">` : '';
                return $(`<span>${img}${data.text}</span>`);
            },
            escapeMarkup: function (markup) { return markup; }
        });
    @endforeach

    $( '#created_at' ).flatpickr( {
        mode: 'range',
        disableMobile: true,
        onClose: function( selected, dateStr, instance ) {
            window[$( instance.element ).data('id')] = $( instance.element ).val();
            dt_table.draw();
        }
    } );
});
</script>
<script src="{{ asset( 'admin/js/dataTable.init.js' ) . Helper::assetVersion() }}"></script>

@php
    function getTableName( $field, $table ) {
        $relatedBase = Str::beforeLast($field, '_id');
        $selfReferencing = [ 'referral' ];

        if (in_array($relatedBase, $selfReferencing)) {
            $relatedTable = $table;
        } else {
            $relatedTable = Str::plural($relatedBase);
        }
        return $relatedTable;
    }
@endphp