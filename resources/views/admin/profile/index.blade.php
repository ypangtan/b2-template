<?php
$profile = 'profile';
?>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>{{ __( 'profile.account_setting' ) }}</strong>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_username" class="col-sm-5 col-form-label">{{ __( 'administrator.username' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $profile }}_username" value="{{ auth()->user()->username }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_email" class="col-sm-5 col-form-label">{{ __( 'administrator.email' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $profile }}_email" value="{{ auth()->user()->email }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <strong>{{ __( 'profile.security_setting' ) }}</strong>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_current_password" class="col-sm-5 col-form-label">{{ __( 'profile.current_password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $profile }}_current_password" placeholder="{{ __( 'template.leave_blank' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_new_password" class="col-sm-5 col-form-label">{{ __( 'profile.new_password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $profile }}_new_password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_confirm_new_password" class="col-sm-5 col-form-label">{{ __( 'profile.confirm_new_password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $profile }}_confirm_new_password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                @if ( config( 'services.mfa.enabled' ) )
                <div class="mb-3 row">
                    <label for="{{ $profile }}_mfa" class="col-sm-5 col-form-label">{{ __( 'mfa.mfa' ) }}</label>
                    <div class="col-sm-7">
                        <div class="col-form-label">
                            @if ( empty( auth()->user()->mfa_secret ) ) 
                            <strong class="text-primary" role="button" id="bind">{{ __( 'mfa.bind_now' ) }}</strong>
                            @else
                            <strong class="text-success">{{ __( 'mfa.binded' ) }}</strong>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="text-end">
                    <button id="{{ $profile }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_mfa_bind" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __( 'mfa.setup_mfa' ) }}</h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    {{ __( 'mfa.first_mfa_step_1' ) }}
                </div>
                <div class="text-center mb-3">
                    <?=$data['mfa_qr'];?>
                </div>
                <div class="text-center mb-3">
                    {{ __( 'mfa.first_mfa_step_2' ) }}
                </div>
                <input class="form-control form-control-sm" placeholder="XXXXXX" id="mfa_code">
                <div class="invalid-feedback"></div>
                <input type="hidden" id="mfa_secret" value="<?=$data['mfa_secret']?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __( 'template.cancel' ) }}</button>
                <button type="button" class="btn btn-sm btn-primary" id="mfa_submit">{{ __( 'template.submit' ) }}</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>{{ __( 'profile.language_setting' ) }}</strong>
                </div>
                <div class="row">
                    <label for="{{ $profile }}_switch_language" class="col-sm-5 col-form-label">{{ __( 'profile.language' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-select form-select-sm" id="{{ $profile }}_switch_language">
@foreach( Config::get( 'languages' ) as $lang => $language )
@if( $lang != App::getLocale() )
                            <option value="{{ $lang }}">{{ $language }}</option>
@else
                            <option value="{{ $lang }}" selected>{{ $language }}</option>
@endif
@endforeach
                        </select>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        $( '#modal_success' ).on( 'hidden.bs.modal', function() {
            window.location.reload();
        } );

        let p = '#profile',
            modalMFABind = new bootstrap.Modal( document.getElementById( 'modal_mfa_bind' ) );

        $( '#bind' ).on( 'click', function() {
            modalMFABind.show();
        } );

        $( '#modal_mfa_bind #mfa_submit' ).on( 'click', function() {

            resetInputValidation();

            $.ajax( {
                url: '{{ route( 'admin.mfa.setupMFA' ) }}',
                type: 'POST',
                data: {
                    code: $( '#mfa_code' ).val(),
                    mfa_secret: $( '#mfa_secret' ).val(),
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    modalMFABind.hide();
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.show();
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( '#mfa_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        modalMFABind.hide();
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.show();       
                    }
                }
            } );
        } );

        $( p + '_submit' ).click( function() {

            resetInputValidation();

            $.ajax( {
                url: '{{ route( 'admin.profile.update' ) }}',
                type: 'POST',
                data: {
                    username: $( p + '_username' ).val(),
                    email: $( p + '_email' ).val(),
                    current_password: $( p + '_current_password' ).val(),
                    new_password: $( p + '_new_password' ).val(),
                    confirm_new_password: $( p + '_confirm_new_password' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.show();
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( p + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.show();       
                    }
                }
            } )
        } );

        $( p + '_switch_language' ).change( function() {

            window.location.href = '{{ Helper::baseAdminUrl() }}/lang/' + $( this ).val();
        } );
    } );
</script>