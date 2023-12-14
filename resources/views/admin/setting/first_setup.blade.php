<div class="card first-mfa">
    <div class="card-body">
        <h5 class="card-title mb-3">{{ __( 'setting.first_mfa_title' ) }}</h5>
        <div class="" id="setup_mfa">
            <div class="mb-3">
                {{ __( 'setting.first_mfa_subtitle' ) }}
            </div>
            <div class="mb-3">
                <strong>{{ __( 'setting.first_mfa_step_1' ) }}</strong>
            </div>
            <div class="mb-3">
                @if ( str_contains( $data['mfa_qr'], 'data:image/png' ) )
                <img src="<?=$data['mfa_qr'];?>" alt="QR" />
                @else
                <?=$data['mfa_qr'];?>
                @endif
            </div>
            <hr>
            <div class="mb-3">
                <strong>{{ __( 'setting.first_mfa_step_2' ) }}</strong>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control form-control-sm" id="mfa_authentication_code" placeholder="{{ __( 'setting.authentication_code' ) }}" value="" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="">
                <button type="button" id="mfa_save" class="btn btn-sm btn-primary">{{ __( 'template.confirm' ) }}</button>
                <button type="button" id="mfa_logout" class="btn btn-sm btn-outline-secondary">{{ __( 'template.logout' ) }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let mfaSecret = '{{ $data['mfa_secret'] }}',
        toast = new bootstrap.Toast( document.getElementById( 'toast' ) );

        $( '#mfa_save' ).click( function() {
            
            let data = {
                authentication_code: $( '#mfa_authentication_code' ).val(),
                mfa_secret: mfaSecret,
                _token: '{{ csrf_token() }}',
            }, that = $( this );

            $.ajax( {
                url: '{{ route( 'admin.setupMFA' ) }}',
                type: 'POST',
                data: data,
                success: function( response ) {
                    
                    $( '#mfa_authentication_code' ).val( '' ).removeClass( 'is-invalid' ).next().text( '' );

                    $( '#toast .toast-body' ).text( '{{ __( 'setting.mfa_setup_complete' ) }}' );
                    $( that ).removeClass( 'disabled' ).html( '{{ __( 'template.submit' ) }}' );
                    toast.show();

                    that.addClass( 'disabled' );

                    setTimeout(function(){
                        window.location.href = '{{ route( 'admin.dashboard.index' ) }}';
                    }, 2000 );

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

        $( '#mfa_logout' ).click( function() {
            $.ajax( {
                url: '{{ route( 'admin.logoutLog' ) }}',
                type: 'POST',
                data: { '_token': '{{ csrf_token() }}' },
                success: function() {
                    document.getElementById( 'logout-form' ).submit();
                }
            } );
        } );
    } );
</script>