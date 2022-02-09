<div class="offcanvas offcanvas-end offcanvas-right" tabindex="-1" id="{{ $crud }}_canvas" aria-labelledby="{{ $crud }}_canvas_label">
    <div class="offcanvas-header">
        <h2 id="{{ $crud }}_canvas_label">{{ $title }}</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
    @foreach( json_decode( html_entity_decode( $contents ) ) as $content )
        @if( $crud == 'role_edit' )
        <x-off-canvas.floating-label-input type="text" class="form-control" id="{{ $crud . $content->id }}" title="{{ $content->title }}" 
            placeholder="{{ $content->placeholder }}" autocomplete="{{ @$content->autocomplete }}" mandatory="{{ @$content->mandatory }}" disabled />    
        @else
        <x-off-canvas.floating-label-input type="text" class="form-control" id="{{ $crud . $content->id }}" title="{{ $content->title }}" 
            placeholder="{{ $content->placeholder }}" autocomplete="{{ @$content->autocomplete }}" mandatory="{{ @$content->mandatory }}" />
        @endif
    @endforeach
        <div class="card">
            <div class="card-body">
            @forelse( \App\Models\Module::all() as $module )
                <div class="mb-4 {{ $crud }}-modules-section" data-module="{{ $module->name }}">
                    <h4>{{ __( 'role.module_title', [ 'module' => __( 'role.' . $module->name ) ] ) }}</h4>
                @forelse( Helper::moduleActions() as $action )
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="{{ $crud . '_' . $action . '_' . $module->name }}" value="{{ $action }}">
                        <label class="form-check-label" for="{{ $crud . '_' . $action . '_' . $module->name }}">{{ __( 'role.action_module', [ 'action' => __( 'role.' . $action ), 'module' => __( 'role.' . $module->name ) ] ) }}</label>
                    </div>
                @empty
                    <p class="text-center">No action found</p>

                @endforelse
                </div>
            @empty
                <h4 class="text-center mb-0">No module found</h4>

            @endforelse

            @if( strpos( $crud, 'edit' ) !== false )
                <input type="hidden" id="{{ $crud }}_id" />
            @endif
            </div>
        </div>

        <div class="offcanvas-button-group">
            <button type="submit" class="btn btn-success" id="{{ $crud }}_submit">{{ __( 'role.save_changes' ) }}</button>&nbsp;&nbsp;<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas" aria-label="Close" id="offcanvas_close">{{ __( 'role.cancel' ) }}</button>
        </div>
    </div>
</div>