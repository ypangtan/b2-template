<?php $columns = json_decode( html_entity_decode( $columns ) ); ?>
@if( $enableFilter == 'true' )
<div class="listing-filter">
@foreach( $columns as $key => $column )
@if( $column->type !== 'default' )
{!! renderFilter( $column->type, $column ) !!}
@endif
@endforeach
</div>
@endif

<table class="table table-striped nowrap" style="width: 100%;" {{ $attributes }}>
    <thead>
        <tr>
            @foreach( $columns as $column )
            <th>{{ $column->title }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody></tbody>
    @if( $enableFooter == 'true' )
    <tfoot>
        <tr>
            @foreach( $columns as $key => $column )
            @if( @$column->preAmount )
            <th class="text-end">{{ __( 'datatables.sub_total' ) }}</th>
            @continue
            @endif
            @if( @$column->amount )
            <th class="subtotal text-end"></th>
            @continue
            @endif
            <th></th>
            @endforeach
        </tr>
        <tr>
            @foreach( $columns as $key => $column )
            @if( @$column->preAmount )
            <th class="text-end">{{ __( 'datatables.grand_total' ) }}</th>
            @continue
            @endif
            @if( @$column->amount )
            <th class="grandtotal text-end"></th>
            @continue
            @endif
            <th></th>
            @endforeach
        </tr>
    </tfoot>
    @endif
</table>

<?php

function renderFilter( $type, $column = [] ) {

    switch( $type ) {
        case 'input':
            $html = '<input type="text" class="form-control form-control-sm" placeholder="' . $column->placeholder . '" id="' . $column->id . '" data-id="' . $column->id . '" />';
            break;
        case 'date':
            $html = '<input type="text" class="form-control form-control-sm" placeholder="' . $column->placeholder . '" id="' . $column->id . '" data-id="' . $column->id . '" style="background-color: #fff;" />';
            break;
        case 'select':
            $html = '<select class="form-control form-control-sm" id="' . $column->id . '" data-id="' . $column->id . '">';
            foreach( $column->options as $option ) {
                $html .= '<option value="' . $option->value . '">' . $option->title . '</option>';
            }
            $html .= '</select>';
            break;
        default:
            $html = '';
            break;
    }

    return $html;
}

?>