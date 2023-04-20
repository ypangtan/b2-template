<?php
$administrator_create = 'administrator_create';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $administrator_create }}_username" class="col-sm-5 col-form-label">{{ __( 'administrator.username' ) }}</label>
                    <div class="col-sm-7">
                        <div class="input-group input-group-sm has-validation">
                            <span class="input-group-text" id="{{ $administrator_create }}_prefix"></span>
                            <input type="text" class="form-control form-control-sm" id="{{ $administrator_create }}_username">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_create }}_email" class="col-sm-5 col-form-label">{{ __( 'administrator.email' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $administrator_create }}_email">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_create }}_fullname" class="col-sm-5 col-form-label">{{ __( 'administrator.fullname' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $administrator_create }}_fullname">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_create }}_password" class="col-sm-5 col-form-label">{{ __( 'administrator.password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $administrator_create }}_password" autocomplete="new-password" placeholder="{{ __( 'template.leave_blank' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_create }}_role" class="col-sm-5 col-form-label">{{ __( 'administrator.role' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-select form-select-sm" id="{{ $administrator_create }}_role">
                            <option value="">{{ __( 'datatables.select_x', [ 'title' => __( 'administrator.role' ) ] ) }}</option>
                            @foreach( $data['roles'] as $role )
                            <option value="{{ $role['value'] }}">{{ $role['title'] }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $administrator_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $administrator_create }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let ac = '#{{ $administrator_create }}',
            roleMapper = @json( $data['roles'] );

        $( ac + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.administrator.index' ) }}';
        } );

        $( ac + '_role' ).on( 'change', function() {

            const pos = roleMapper.map( e => e.value ).indexOf( parseInt( $( this ).val() ) );

            if ( [ 'jpj', 'puspakom' ].includes( roleMapper[pos].key ) ) {
                $( ac + '_prefix' ).html( roleMapper[pos].key + '-' );
            } else {
                $( ac + '_prefix' ).html( '' );
            }
        } );

        $( ac + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'username', $( ac + '_prefix' ).html() + $( ac + '_username' ).val() );
            formData.append( 'email', $( ac + '_email' ).val() );
            formData.append( 'fullname', $( ac + '_fullname' ).val() );
            formData.append( 'password', $( ac + '_password' ).val() );
            formData.append( 'role', $( ac + '_role' ).val() );
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.administrator.createAdministrator' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.administrator.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ac + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
        } );
    } );
</script>