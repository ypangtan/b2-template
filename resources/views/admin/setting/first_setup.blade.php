<div class="card first-mfa">
    <div class="card-body">
        <h5 class="card-title mb-3">{{ __( 'mfa.first_mfa_title' ) }}</h5>
        <div class="" id="setup_mfa">
            <div class="mb-3">
                {{ __( 'mfa.first_mfa_subtitle' ) }}
            </div>
            <div class="mb-3">
                <strong>{{ __( 'mfa.first_mfa_step_1' ) }}</strong>
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
                <strong>{{ __( 'mfa.first_mfa_step_2' ) }}</strong>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control form-control-sm" id="mfa_code" placeholder="XXXXXX"/>
                <div class="invalid-feedback"></div>
            </div>
            <div class="">
                <button type="button" id="mfa_save" class="btn btn-sm btn-primary">{{ __( 'template.confirm' ) }}</button>
                <button type="button" id="mfa_logout" class="btn btn-sm btn-outline-secondary">{{ __( 'template.logout' ) }}</button>
            </div>
        </div>
    </div>
</div>

<x-modal-success />
<x-modal-danger />

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let mfaSecret = '{{ $data['mfa_secret'] }}';

        let modalSuccess = new bootstrap.Modal( document.getElementById( 'modal_success' ) ),
            modalDanger = new bootstrap.Modal( document.getElementById( 'modal_danger' ) );

        $( '#mfa_save' ).click( function() {

            resetInputValidation();
            
            let data = {
                code: $( '#mfa_code' ).val(),
                mfa_secret: mfaSecret,
                _token: '{{ csrf_token() }}',
            }, that = $( this );

            $.ajax( {
                url: '{{ route( 'admin.mfa.setupMFA' ) }}',
                type: 'POST',
                data: data,
                success: function( response ) {

                    $( that ).removeClass( 'disabled' ).html( '{{ __( 'template.submit' ) }}' );
                    that.addClass( 'disabled' );

                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.show();

                    setTimeout(function(){
                        window.location.href = '{{ route( 'admin.dashboard.index' ) }}';
                    }, 2000 );
                },
                error: function( error ) {                    
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