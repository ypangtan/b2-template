<div class="form-floating mb-3">
    <input type="{{ $type }}" {{ $attributes }} />
    <div class="invalid-feedback"></div>
    <label for="{{ $attributes->get( 'id' ) }}">{{ $title }}{!! $mandatory ? '<span class="required">*</span>' : '' !!}</label>
    @if( $smalltext != '' )
    <small>{{ $smalltext }}</small>
    @endif
</div>