<?php
$announcement_edit = 'announcement_edit';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_status" class="col-sm-4 col-form-label">{{ __( 'datatables.status' ) }}</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm form-control-plaintext" id="{{ $announcement_edit }}_status" value="{{ $data['announcement']['deleted_at'] ? __( 'datatables.unpublished' ) : __( 'datatables.published' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_title" class="col-sm-4 col-form-label">{{ __( 'announcement.type' ) }}</label>
                    <div class="col-sm-8">
                        <select class="form-control form-control-sm" id="{{ $announcement_edit }}_type">
                            <option value="2" {{ $data['announcement']['type'] == 2 ? 'selected' : '' }}>{{ __( 'announcement.news' ) }}</option>
                            <option value="3" {{ $data['announcement']['type'] == 3 ? 'selected' : '' }}>{{ __( 'announcement.event' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_title" class="col-sm-4 col-form-label">{{ __( 'announcement.title' ) }}</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" id="{{ $announcement_edit }}_title" value="{{ $data['announcement']['title'] }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_content" class="col-sm-4 col-form-label">{{ __( 'announcement.content' ) }}</label>
                    <div class="col-sm-8">
                        <textarea class="form-control form-control-sm" id="{{ $announcement_edit }}_content" rows="10">{{ $data['announcement']['content'] }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_image" class="col-sm-4 col-form-label">{{ __( 'announcement.image' ) }}</label>
                    <div class="col-sm-8">
                    <div style="position: relative; text-align: right">
                        <img src="{{ $data['announcement']['image'] ? $data['announcement']['path'] : asset( 'admin/img/placeholder/fff.jpg' ) }}" id="{{ $announcement_edit }}_image_preview" style="width: 50%;">
                        <i class="{{ $data['announcement']['image'] ? '' : 'hidden' }} click-action" id="{{ $announcement_edit }}_image_remove" style="position: absolute; top: 5px; right: 5px; stroke-width: 3; width: 24px; height: 24px" icon-name="x-circle" color="#f50d0d"></i>
                    </div>
                    <input type="file" id="{{ $announcement_edit }}_image" class="hidden" accept="image/png, image/gif, image/jpeg">
                    <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $announcement_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $announcement_edit }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let originalImage = '{{ $data['announcement']['path'] }}',
            ae = '#announcement_edit';

        $( ae + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.announcement.index' ) }}';
        } );

        $( ae + '_image_preview' ).click( function() {

            var that = $( this ),
                target = $( ae + '_image' );

            target.trigger( 'click' );

            target.change( function() {

                if( $( this ).prop('files')[0] != undefined ) {
                    that.attr( 'src', URL.createObjectURL( $( this ).prop('files')[0] ) );
                    $( ae + '_image_remove' ).removeClass( 'hidden' );
                    target.next().text( '' );
                } else {
                    $( ae + '_image_remove' ).addClass( 'hidden' );
                    that.attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );
                }
            } );
        } );

        $( document ).on( 'click', ae + '_image_remove', function() {

            $( this ).addClass( 'hidden' );
            $( ae + '_image' ).val( '' );
            $( ae + '_image_preview' ).attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );

        } );

        $( ae + '_submit' ).click( function() {

            resetInputValidation();

            let formData = new FormData();

            formData.append( 'id', '{{ Helper::encode( request( 'id' ) ) }}' );
            formData.append( 'type', $( ae + '_type' ).val() );
            formData.append( 'title', $( ae + '_title' ).val() );
            formData.append( 'content', $( ae + '_content' ).val() );

            if( $( ae + '_image' )[0].files.length != 0 ) {
                formData.append( 'image', $( ae + '_image' )[0].files[0] );
            } else {
                if ( $( ae + '_image_preview' ).attr( 'src' ) == "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" ) {
                    formData.append( 'image_remove', '1' );    
                }
                formData.append( 'image', '' );
            }

            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.announcement.updateAnnouncement' ) }}',
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
                            $( ae + '_' + key ).addClass( 'is-invalid' ).next().text( value );
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