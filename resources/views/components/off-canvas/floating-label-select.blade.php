<div class="form-floating mb-3">
    <select {{ $attributes }} >
        <option value="" selected>{{ __( 'datatables.select_x', [ 'title' => $title ] ) }}</option>
        @foreach( json_decode( html_entity_decode( $options ) ) as $option )
        <option value="{{ $option->value }}">{{ $option->title }}</option>
        @endforeach
    </select>
    <div class="invalid-feedback"></div>
    <label for="{{ $attributes->get( 'id' ) }}">{{ $title }}{!! $mandatory ? '<span class="required">*</span>' : '' !!}</label>
</div>