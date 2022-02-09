<div class="form-floating mb-3">
    <textarea {{ $attributes }} style="height: 150px"></textarea>
    <div class="invalid-feedback"></div>
    <label for="{{ $attributes->get( 'id' ) }}">{{ $title }}{!! $mandatory ? '<span class="required">*</span>' : '' !!}</label>
</div>