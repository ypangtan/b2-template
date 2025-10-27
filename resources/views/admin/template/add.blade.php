<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$languages = Config::get('languages');
$languageKeys = array_keys($languages);

$modelClass = $data['model'];
$model = new $modelClass();
$table = $model->getTable();
$columns = $modelClass::$showEditableAttribute;
$template_create = $table . '_create';
$select2Fields = [];
$multiLangFields = [];

$autoColumns = [];

foreach ($columns as $col) {
    $colType = DB::getSchemaBuilder()->getColumnType($table, $col);

    if ( Str::endsWith( $col, '_id' ) ) {
        $select2Fields[] = $col;
        $autoColumns[] = (object)[
            'type' => 'select2',
            'id' => $col,
            'title' => __( $table . '.' . Str::singular( Str::beforeLast( $col, '_id' ) ) ),
            'placeholder' => __("datatables.search_x", ['title' => __("$table.$col")]),
        ];
    } elseif (in_array($col, ['status', 'type'])) {
        $options = $data[$col];
        array_unshift( $options, [ 'value' => '', 'title' => __( 'datatables.all_x', [ 'title' => __( "$table.$col" ) ] ) ] );
        $autoColumns[] = (object)[
            'type' => 'select',
            'id' => $col,
            'title' => __( "$table.$col"),
            'options' => $options,
        ];
    }  elseif ( in_array( $col, [ 'phone_number', 'password' ] ) ) {
        $autoColumns[] = (object)[
            'type' => $col,
            'id' => $col,
            'title' => __( $table . '.' . Str::singular( Str::beforeLast( $col, '_id' ) ) ),
        ];
    }  elseif ( str_starts_with( $col, 'multi_lang_' ) ) {
        $multiLangFields[] = (object)[
            'type' => 'multi_lang',
            'input_type' => $colType == 'string' ? 'text' : 'textarea',
            'id' => $col,
            'title' => __( $table . '.' . Str::singular( Str::beforeLast( $col, '_id' ) ) ),
        ];
        $autoColumns[] = (object)[
            'type' => 'multi_lang',
            'id' => $col,
        ];
    } else {
        switch ($colType) {
            case 'decimal':
                $inputType = 'number';
                break;
            default:
                $inputType = 'text';
        }
        $autoColumns[] = (object)[
            'type' => $inputType,
            'id' => $col,
            'title' => __( "$table.$col"),
        ];
    }
}
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                @if( count( $multiLangFields ) > 0 )
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist" style="gap:20px;">
                        @foreach ( $languages as $lang => $langName )
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $lang }}_name-tab" data-bs-toggle="tab" data-bs-target="#{{ $lang }}_name" type="button" role="tab" aria-controls="{{ $lang }}_name" aria-selected="{{ $loop->first ? 'true' : 'false' }}"> {{ $langName }} </button>
                        @endforeach
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    @foreach ($languages as $lang => $langName)
                    <div class="tab-pane fade pt-4 {{ $loop->first ? 'show active' : '' }}" id="{{ $lang }}_name" role="tabpanel" aria-labelledby="{{ $lang }}_name-tab">
                        @foreach( $multiLangFields as $i => $v )
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $lang }}_{{ $v->id }}" class="col-sm-4 col-form-label">{{ __( $table . '.' . $v->id ) }} ( {{ $langName }} )</label>
                                <div class="col-sm-8">
                                    @if( $v->input_type == 'text' )
                                    <input type="text" class="form-control form-control-sm" id="{{ $template_create }}_{{ $lang }}_{{ $v->id }}">
                                    @else
                                    <textarea class="form-control form-control-sm" id="{{ $template_create }}_{{ $lang }}_{{ $v->id }}" rows="10"></textarea>
                                    @endif
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endIf

                @foreach ( $autoColumns as $i => $v )
                    @switch( $v->type )
                        @case( 'select2' )
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $v->id }}" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <select class="form-control select2" id="{{ $template_create }}_{{ $v->id }}" data-placeholder="{{ $v->placeholder }}"></select>
                                </div>
                            </div>
                            @break
                        @case('select')
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_role" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <select class="form-select form-select-sm" id="{{ $template_create }}_role">
                                        @foreach( $v->options as $option )
                                        <option value="{{ $option->value }}">{{ $option->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            @break
                        @case('text')
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $v->id }}" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control form-control-sm" id="{{ $template_create }}_{{ $v->id }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            @break
                        @case('number')
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $v->id }}" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control form-control-sm" id="{{ $template_create }}_{{ $v->id }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            @break
                        @case('password')
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $v->id }}" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control form-control-sm" id="{{ $template_create }}_{{ $v->id }}" autocomplete="new-password">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            @break
                        @case( 'phone_number' )
                            <input type="hidden" id="{{ $template_create }}_calling_code" value="+60">
                            <div class="mb-3 row">
                                <label for="{{ $template_create }}_{{ $v->id }}" class="col-sm-5 col-form-label">{{ __( $model . '.' . $v->id ) }}</label>
                                <div class="col-sm-7">
                                    <div class="input-group phone-number">
                                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #ced4da;">+60</button>
                                        <ul class="dropdown-menu" id="phone_number_country">
                                            <li class="dropdown-item" data-call-code="+60">+60</li>
                                            <li class="dropdown-item" data-call-code="+65">+65</li>
                                        </ul>
                                        <input type="text" class="form-control form-control-sm" id="{{ $template_create }}_{{ $v->id }}">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            @break
                        @case( 'multi_lang' )
                            @break;
                    @endswitch
                @endforeach

                <div class="text-end">
                    <button id="{{ $template_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $template_create }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset( 'admin/css/ckeditor/styles.css' ) }}">
<script src="{{ asset( 'admin/js/ckeditor/ckeditor.js' ) }}"></script>
<script src="{{ asset( 'admin/js/ckeditor/upload-adapter.js' ) }}"></script>

<script>
window.ckeupload_path = '{{ route( 'admin.file.ckeUpload' ) }}';
window.csrf_token = '{{ csrf_token() }}';
window.cke_element = @json(
    collect($multiLangFields)
        ->map(fn($v) => collect($languageKeys)
            ->map(fn($lang) => $v->input_type === 'textarea' ? "{$template_create}_{$lang}_{$v->id}" : null)
            ->filter()
            ->values()
        )
        ->flatten()
        ->values()
);

</script>
<script src="{{ asset( 'admin/js/ckeditor/ckeditor-init-multi.js' ) }}"></script>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let uc = '#{{ $template_create }}';

        $( uc + '_cancel' ).click( function() {
            window.location.href = '{{ route("admin.module_parent." . Str::singular( $table ) . ".index") }}';
        } );
 
        $( '.dropdown-item' ).on( 'click', function() {
            let callingCode = $( this ).data( 'call-code' );
            $( '.phone-number > button' ).html( callingCode );
            $( uc + '_calling_code' ).val( callingCode );
        } );

        $( uc + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            @foreach ( $autoColumns as $i => $v )
                @if( $v->type == 'multi_lang' )
                    @foreach( $languageKeys as $lang )
                        const editorKey = `template_create_${lang}_${v->id}`;

                        const descVal = editors[editorKey] ? editors[editorKey].getData() : '';

                        formData.append( `${lang}_${v->id}`, descVal );
                    @endforeach
                @else
                formData.append( '{{ $v->id }}', $( uc + '_' + '{{ $v->id }}' ).val() ?? '' );
                @endif
            @endforeach
            formData.append( '_token', '{{ csrf_token() }}' );
            let createRoute = '{{ route("admin." . Str::singular($table) . ".create" . Str::studly( Str::singular($table) )) }}';
            
            $.ajax( {
                url: createRoute,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.user.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( uc + '_' + key ).addClass( 'is-invalid' ).nextAll( '.invalid-feedback' ).text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
        } );
        
        @foreach( $select2Fields as $field )
            console.log( uc + '_{{ $field }}' );
            @php
            $routeName = 'admin.' . Str::singular( getTableName( $field, $table )  ) . '.all' . Str::studly( getTableName( $field, $table )  );
            @endphp
            $( uc + '_{{ $field }}' ).select2({
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
    } );
</script>

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