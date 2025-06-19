<?php
$user_edit = 'user_edit';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_referral" class="col-sm-5 col-form-label">{{ __( 'user.referral' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $user_edit }}_referral" data-placeholder="{{ __( 'datatables.search_x', [ 'title' => __( 'template.users' ) ] ) }}"></select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_username" class="col-sm-5 col-form-label">{{ __( 'user.username' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $user_edit }}_username">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_email" class="col-sm-5 col-form-label">{{ __( 'user.email' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $user_edit }}_email">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <input type="hidden" id="{{ $user_edit }}_calling_code" value="+60">

                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_phone_number" class="col-sm-5 col-form-label">{{ __( 'user.phone_number' ) }}</label>
                    <div class="col-sm-7">
                        <div class="input-group phone-number">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #ced4da;">+60</button>
                            <ul class="dropdown-menu" id="phone_number_country">
                                <li class="dropdown-item" data-call-code="+60">+60</li>
                                <li class="dropdown-item" data-call-code="+65">+65</li>
                            </ul>
                            <input type="text" class="form-control form-control-sm" id="{{ $user_edit }}_phone_number">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_password" class="col-sm-5 col-form-label">{{ __( 'user.password' ) }}</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control form-control-sm" id="{{ $user_edit }}_password" autocomplete="new-password" placeholder="{{ __( 'template.leave_blank' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $user_edit }}_security_pin" class="col-sm-5 col-form-label">{{ __( 'user.security_pin' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $user_edit }}_security_pin" placeholder="{{ __( 'template.leave_blank' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $user_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $user_edit }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        getUser();

        let ue = '#{{ $user_edit }}';

        $( ue + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.user.index' ) }}';
        } );

        $( '.dropdown-item' ).on( 'click', function() {
            let callingCode = $( this ).data( 'call-code' );
            $( '.phone-number > button' ).html( callingCode );
            $( ue + '_calling_code' ).val( callingCode );
        } );

        $( ue + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'referral', $( ue + '_referral' ).val() ?? '' );
            formData.append( 'username', $( ue + '_username' ).val() );
            formData.append( 'email', $( ue + '_email' ).val() );
            formData.append( 'calling_code', $( ue + '_calling_code' ).val() );
            formData.append( 'phone_number', $( ue + '_phone_number' ).val() );
            formData.append( 'password', $( ue + '_password' ).val() );
            formData.append( 'security_pin', $( ue + '_security_pin' ).val() );
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.user.updateUser' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.user.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ue + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
        } );

        referralSelect2 = $( ue + '_referral' ).select2({

            theme: 'bootstrap-5',
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
            closeOnSelect: true,

            ajax: { 
                url: '{{ route( 'admin.user.allUsers' ) }}',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {

                    return {
                        name: params.term, // search term
                        no_user: '{{ Request( 'id' ) }}',
                        no_downline: '{{ Request( 'id' ) }}',
                        designation: 1,
                        start: ( ( params.page ? params.page : 1 ) - 1 ) * 10,
                        length: 10,
                        _token: '{{ csrf_token() }}',
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    let processedResult = [];

                    data.users.map( function( v, i ) {
                        processedResult.push( {
                            id: v.encrypted_id,
                            text: v.username,
                        } );
                    } );

                    return {
                        results: processedResult,
                        pagination: {
                            more: ( params.page * 10 ) < data.recordsFiltered
                        }
                    };
                },
                cache: true
            }

        });

        function getUser() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.user.oneUser' ) }}',
                type: 'POST',
                data: {
                    id: '{{ request( 'id' ) }}',
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    if( response.upline != null ){
                        let option1 = new Option( response.upline.username, response.upline.encrypted_id, true, true );
                        referralSelect2.append( option1 );
                        referralSelect2.trigger( 'change' );
                    }
                    $( ue + '_username' ).val( response.username );
                    $( ue + '_email' ).val( response.email );
                    $( ue + '_calling_code' ).val( response.calling_code );
                    $( '.phone-number > button' ).html( response.calling_code )
                    $( ue + '_phone_number' ).val( response.phone_number );

                    $( 'body' ).loading( 'stop' );
                },
            } );
        }
    } );
</script>