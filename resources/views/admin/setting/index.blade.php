<div class="row">
    <div class="col-md-3 col-xl-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __( 'setting.settings' ) }}</h5>
            </div>
            <div class="list-group list-group-flush" role="tablist">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#mfa" role="tab">{{ __( 'setting.setting_1' ) }}</a>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-xl-10">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="mfa" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __( 'setting.setting_1' ) }}</h5>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {
        $( '.list-group-item-action' ).click( function() {
            $( '.list-group-item-action' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
        } );

        let submitting = '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><span class="sr-only">{{ __( 'template.loading' ) }}</span>';
    } );
</script>

