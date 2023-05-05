<?php
$announcement_create = 'announcement_create';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $announcement_create }}_title" class="col-sm-4 col-form-label">{{ __( 'datatables.type' ) }}</label>
                    <div class="col-sm-8">
                        <select class="form-control form-control-sm" id="{{ $announcement_create }}_type">
                            <option value="2">{{ __( 'announcement.news' ) }}</option>
                            <option value="3">{{ __( 'announcement.event' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_create }}_title" class="col-sm-4 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" id="{{ $announcement_create }}_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_create }}_content" class="col-sm-4 col-form-label">{{ __( 'announcement.content' ) }}</label>
                    <div class="col-sm-8">
                        <textarea class="form-control form-control-sm" id="{{ $announcement_create }}_content" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_create }}_image" class="col-sm-4 col-form-label">{{ __( 'announcement.image' ) }}</label>
                    <div class="col-sm-8">
                        <div style="position: relative; text-align: right">
                            <img src="{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" id="{{ $announcement_create }}_image_preview" style="width: 50%;">
                            <i class="hidden click-action" id="{{ $announcement_create }}_image_remove" style="position: absolute; top: 5px; right: 5px; stroke-width: 3; width: 24px; height: 24px" icon-name="x-circle" color="#f50d0d"></i>
                        </div>
                        <input type="file" id="{{ $announcement_create }}_image" class="hidden" accept="image/png, image/gif, image/jpeg">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $announcement_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $announcement_create }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {
        
        let ac = '#announcement_create';

        $( ac + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.announcement.index' ) }}';
        } );

        $( document ).on( 'click', ac + '_image_remove', function() {

            $( this ).addClass( 'hidden' );
            $( ac + '_image' ).val( '' );
            $( ac + '_image_preview' ).attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );

        } );

        $( ac + '_image_preview' ).click( function() {

            var that = $( this ),
                target = $( ac + '_image' );

            target.trigger( 'click' );

            target.change( function() {

                if( $( this ).prop('files')[0] != undefined ) {
                    that.attr( 'src', URL.createObjectURL( $( this ).prop('files')[0] ) );
                    $( ac + '_image_remove' ).removeClass( 'hidden' );
                    target.next().text( '' );
                } else {
                    $( ac + '_image_remove' ).addClass( 'hidden' );
                    that.attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );
                }
            } );
        } );

        $( ac + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();

            formData.append( 'type', $( ac + '_type' ).val() );
            formData.append( 'title', $( ac + '_title' ).val() );
            formData.append( 'content', $( ac + '_content' ).val() );

            if( $( ac + '_image' )[0].files.length != 0 ) {
                formData.append( 'image', $( ac + '_image' )[0].files[0] );
            } else {
                formData.append( 'image', '' );
            }

            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.announcement.createAnnouncement' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function ( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.announcement.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ac + '_' + key ).addClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).text( value );
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