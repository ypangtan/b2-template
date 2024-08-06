<?php
$profile = 'profile';
?>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 row">
                    <label for="{{ $profile }}_username" class="col-sm-5 col-form-label">{{ __( 'administrator.username' ) }}</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $profile }}_username" value="{{ auth()->user()->username }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $profile }}_email" class="col-sm-5 col-form-label">{{ __( 'administrator.email' ) }}</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $profile }}_email" value="{{ auth()->user()->email }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $profile }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <label for="{{ $profile }}_switch_language" class="col-sm-5 col-form-label">{{ __( 'profile.language' ) }}</label>
                    <div class="col-md-7">
                        <select class="form-control form-control-sm" id="{{ $profile }}_switch_language">
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

        let p = '#profile';

        $( p + '_submit' ).click( function() {

            resetInputValidation();

            $.ajax( {
                url: '{{ route( 'admin.profile.update' ) }}',
                type: 'POST',
                data: {
                    username: $( p + '_username' ).val(),
                    email: $( p + '_email' ).val(),
                    '_token': '{{ csrf_token() }}',
                },
                success: function( response ) {

                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();
                },
                error: function( error ) {
                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( p + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } )
        } );

        $( p + '_switch_language' ).change( function() {

            window.location.href = '{{ Helper::baseAdminUrl() }}/lang/' + $( this ).val();
        } );
    } );
</script>