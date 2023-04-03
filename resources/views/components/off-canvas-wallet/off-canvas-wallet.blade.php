<div class="offcanvas offcanvas-end offcanvas-right" tabindex="-1" id="{{ $crud }}_canvas" aria-labelledby="service_add_canvas_label">
    <div class="offcanvas-header">
        <h2 id="{{ $crud }}_canvas_label" class="mb-0">{{ __( 'wallet.update_wallet' ) }}</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
    @foreach( json_decode( html_entity_decode( $contents ) ) as $content )
        @if( $content->id == '_amount' )
        <x-off-canvas.floating-label-input type="text" class="form-control" id="{{ $crud . $content->id }}" title="{{ $content->title }}" 
            placeholder="{{ $content->placeholder }}" autocomplete="{{ @$content->autocomplete }}" mandatory="{{ @$content->mandatory }}" />
        @else
        <x-off-canvas.floating-label-input type="text" class="form-control" id="{{ $crud . $content->id }}" title="{{ $content->title }}" 
            placeholder="{{ $content->placeholder }}" autocomplete="{{ @$content->autocomplete }}" mandatory="{{ @$content->mandatory }}" readonly />
        @endif
    @endforeach
        <div class="offcanvas-button-group">
            <input type="hidden" name="id" id="{{ $crud }}_id">
            <input type="hidden" name="user_id" id="{{ $crud }}_user_id">
            <button type="submit" class="btn btn-success" id="{{ $crud }}_submit">{{ __( 'wallet.save_changes' ) }}</button>&nbsp;&nbsp;<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas" aria-label="Close" id="offcanvas_close">{{ __( 'wallet.cancel' ) }}</button>
        </div>
    </div>
</div>