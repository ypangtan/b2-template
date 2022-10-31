<!-- <h1 class="h3 mb-3">{{ __( 'setting.settings' ) }}</h1> -->

<div class="row">
    <div class="col-md-3 col-xl-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __( 'setting.settings' ) }}</h5>
            </div>
            <div class="list-group list-group-flush" role="tablist">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#mfa" role="tab">{{ __( 'setting.mfa' ) }}</a>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-xl-10">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="mfa" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __( 'setting.setup_mfa' ) }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 {{ auth()->user()->mfa_secret == '' ? 'hidden' : '' }}" id="done_mfa">
                                <div class="mb-3 description">
                                    {{ __( 'setting.reset_mfa_description' ) }}
                                </div>
                                <div class="mb-3 hidden reset-one-time-password">
                                    <label class="form-label" for="mfa_reset_one_time_password">{{ __( 'setting.one_time_password' ) }}</label>
                                    <input type="text" class="form-control" id="mfa_reset_one_time_password" placeholder="{{ __( 'setting.one_time_password' ) }}" value="" />
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="">
                                    <button type="button" id="mfa_reset_cancel" class="btn btn-outline-secondary hidden">{{ __( 'template.cancel' ) }}</button>
                                    <button type="button" id="mfa_reset" class="btn btn-danger" data-step="1">{{ __( 'setting.reset_mfa' ) }}</button>
                                </div>
                            </div>
                            <div class="col-md-5 {{ auth()->user()->mfa_secret == '' ? '' : 'hidden' }}" id="setup_mfa">
                                <div class="mb-3">
                                    <?=$data['mfa_qr'];?>
                                </div>
                                <div class="mb-3">
                                    {{ __( 'setting.setup_mfa_description' ) }}
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="mfa_one_time_password">{{ __( 'setting.one_time_password' ) }}</label>
                                    <input type="text" class="form-control" id="mfa_one_time_password" placeholder="{{ __( 'setting.one_time_password' ) }}" value="" />
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="">
                                    <button type="button" id="mfa_save" class="btn btn-success">{{ __( 'template.submit' ) }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-toast/>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {
        $( '.list-group-item-action' ).click( function() {
            $( '.list-group-item-action' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
        } );

        let mfaSecret = '{{ $data['mfa_secret'] }}',
            toast = new bootstrap.Toast( document.getElementById( 'toast' ) );
            submitting = '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span><span class="sr-only">{{ __( 'template.loading' ) }}</span>';

        $( '#mfa_reset_cancel' ).click( function() {
            cancelResetMFA();
        } );

        function cancelResetMFA() {
            $( '#mfa_reset' ).html( '{{ __( 'setting.reset_mfa' ) }}' );
            $( '#mfa_reset' ).data( 'step', 1 );
            $( '#done_mfa .description' ).html( '{{ __( 'setting.reset_mfa_description' ) }}' );
            $( '#done_mfa .reset-one-time-password' ).addClass( 'hidden' );
            $( '#done_mfa #mfa_reset_cancel' ).addClass( 'hidden' );
        }

        $( '#mfa_reset' ).click( function() {

            if( $( this ).hasClass( 'disabled' ) ) return;

            let that = $( this );

            switch ( $( this ).data( 'step' ) ) {
                case 1:
                    $( '#mfa_reset' ).html( '{{ __( 'template.submit' ) }}' );
                    $( '#mfa_reset' ).data( 'step', 2 );
                    $( '#done_mfa .description' ).html( '{{ __( 'setting.confirm_reset_mfa_description' ) }}' );
                    $( '#done_mfa .reset-one-time-password' ).removeClass( 'hidden' );
                    $( '#done_mfa #mfa_reset_cancel' ).removeClass( 'hidden' );
                    $( '#mfa_reset_one_time_password' ).val( '' ).removeClass( 'is-invalid' ).next().text( '' );
                    break;
                case 2:

                    $( this ).html( submitting );
                    $( this ).addClass( 'disabled' );

                    $.ajax( {
                        url: '{{ Helper::baseAdminUrl() }}/settings/reset_mfa',
                        type: 'POST',
                        data: {
                            one_time_password: $( '#mfa_reset_one_time_password' ).val(),
                            _token: '{{ csrf_token() }}',  
                        },
                        success: function( response ) {
                            
                            $( '#mfa_reset_one_time_password' ).val( '' ).removeClass( 'is-invalid' ).next().text( '' );

                            $( '#toast .toast-body' ).text( '{{ __( 'setting.mfa_reset_complete' ) }}' );
                            $( that ).removeClass( 'disabled' ).html( '{{ __( 'setting.reset_mfa' ) }}' );
                            toast.show();

                            cancelResetMFA();

                            $( '#done_mfa' ).addClass( 'hidden' );
                            $( '#setup_mfa' ).removeClass( 'hidden' );
                        },
                        error: function( error ) {

                            console.log( error );
                            
                            if( error.status === 422 ) {

                                let errors = error.responseJSON.errors;

                                $.each( errors, function( key, value ) {
                                    $( '#mfa_reset_' + key ).addClass( 'is-invalid' ).next().text( value );
                                } );
                            }

                            $( that ).removeClass( 'disabled' ).html( '{{ __( 'template.submit' ) }}' );
                        }
                    } );
                    break;
                default:
                    break;
            }
        } );

        $( '#mfa_save' ).click( function() {

            if( $( this ).hasClass( 'disabled' ) ) return;

            $( this ).html( submitting );
            $( this ).addClass( 'disabled' );

            let data = {
                one_time_password: $( '#mfa_one_time_password' ).val(),
                mfa_secret: mfaSecret,
                _token: '{{ csrf_token() }}',
            }, that = $( this );

            $( 'input.form-control' ).removeClass( 'is-invalid' );
            $( '.invalid-feedback' ).text( '' );

            $.ajax( {
                url: '{{ Helper::baseAdminUrl() }}/settings/setup_mfa',
                type: 'POST',
                data: data,
                success: function( response ) {
                    
                    $( '#mfa_one_time_password' ).val( '' ).removeClass( 'is-invalid' ).next().text( '' );

                    $( '#toast .toast-body' ).text( '{{ __( 'setting.mfa_setup_complete' ) }}' );
                    $( that ).removeClass( 'disabled' ).html( '{{ __( 'template.submit' ) }}' );
                    toast.show();

                    $( '#done_mfa' ).removeClass( 'hidden' );
                    $( '#setup_mfa' ).addClass( 'hidden' );
                },
                error: function( error ) {

                    console.log( error );
                    
                    if( error.status === 422 ) {

                        let errors = error.responseJSON.errors;

                        $.each( errors, function( key, value ) {
                            $( '#mfa_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }

                    $( that ).removeClass( 'disabled' ).html( '{{ __( 'template.submit' ) }}' );
                }
            } );
        } );
    } );
</script>

