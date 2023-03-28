<div class="card verify-mfa">
    <div class="card-body">
        <h5 class="card-title mb-3">{{ __( 'setting.verify_mfa_title' ) }}</h5>
        <div class="mb-3">
            {{ __( 'setting.verify_mfa_subtitle' ) }}
        </div>
        <div class="mb-3">
            <input type="text" class="form-control form-control-sm" id="authentication_code" placeholder="{{ __( 'setting.authentication_code' ) }}" value="" />
            <div class="invalid-feedback"></div>
        </div>    
        <div class="">
            <button type="button" id="submit" class="btn btn-sm btn-primary">{{ __( 'template.confirm' ) }}</button>
            <button type="button" id="logout" class="btn btn-sm btn-outline-secondary">{{ __( 'template.logout' ) }}</button>
        </div>    
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        $( '#submit' ).click( function() {

            $.ajax( {
                url: '{{ route( 'admin.verifyCode' ) }}',
                type: 'POST',
                data: {
                    authentication_code: $( '#authentication_code' ).val(),
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    console.log( response );

                    if( response.status ) {
                        window.location.href = '{{ route( 'admin.dashboard.index' ) }}';
                    }
                },
                error: function( error ) {

                    console.log( error );

                    if( error.status === 422 ) {

                        let errors = error.responseJSON.errors;

                        $.each( errors, function( key, value ) {
                            $( '#' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    }
                }
            } );
        } );

        $( '#logout' ).click( function() {
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