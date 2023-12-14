<?php
$administrator_edit = 'administrator_edit';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $administrator_edit }}_username" class="col-sm-5 col-form-label">{{ __( 'administrator.username' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $administrator_edit }}_username">
                        <div class="invalid-feedback"></div>
                    </div>
                </div><div class="mb-3 row">
                    <label for="{{ $administrator_edit }}_email" class="col-sm-5 col-form-label">{{ __( 'administrator.email' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $administrator_edit }}_email">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_edit }}_fullname" class="col-sm-5 col-form-label">{{ __( 'administrator.fullname' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $administrator_edit }}_fullname">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_edit }}_password" class="col-sm-5 col-form-label">{{ __( 'administrator.password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $administrator_edit }}_password" autocomplete="new-password" placeholder="{{ __( 'template.leave_blank' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $administrator_edit }}_role" class="col-sm-5 col-form-label">{{ __( 'administrator.role' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-select form-select-sm" id="{{ $administrator_edit }}_role">
                            <option value="">{{ __( 'datatables.select_x', [ 'title' => __( 'administrator.role' ) ] ) }}</option>
                            @foreach( $data['roles'] as $role )
                            <option value="{{ $role['value'] }}">{{ $role['title'] }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $administrator_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $administrator_edit }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let ae = '#{{ $administrator_edit }}',
            roleMapper = @json( $data['roles'] );
        
        $( ae + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.administrator.index' ) }}';
        } );

        $( ae + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'username', $( ae + '_username' ).val() );
            formData.append( 'email', $( ae + '_email' ).val() );
            formData.append( 'fullname', $( ae + '_fullname' ).val() );
            formData.append( 'password', $( ae + '_password' ).val() );
            formData.append( 'role', $( ae + '_role' ).val() );
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.administrator.updateAdministrator' ) }}',
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
                            $( ae + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
        } );

        getAdministrator();

        function getAdministrator() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.administrator.oneAdministrator' ) }}',
                type: 'POST',
                data: {
                    'id': '{{ request( 'id' ) }}',
                    '_token': '{{ csrf_token() }}'
                },
                success: function( response ) {

                    $( ae + '_username' ).val( response.username );
                    $( ae + '_email' ).val( response.email );
                    $( ae + '_fullname' ).val( response.name );
                    $( ae + '_role' ).val( response.role );

                    $( 'body' ).loading( 'stop' );
                },
            } );
        }
    } );
</script>