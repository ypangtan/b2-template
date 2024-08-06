<?php
$announcement_edit = 'announcement_edit';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_status" class="col-sm-5 col-form-label">{{ __( 'datatables.status' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control-plaintext" id="{{ $announcement_edit }}_status">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                        <label class="mb-1">{{ __( 'datatables.photo' ) }}</label>
                        <div class="dropzone" id="{{ $announcement_edit }}_photo" style="min-height: 0px;">
                            <div class="dz-message needsclick">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">{{ __( 'template.drop_file_or_click_to_upload' ) }}</h3>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.type' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-select form-select-sm" id="{{ $announcement_edit }}_type">
                            <option value="2">{{ __( 'announcement.news' ) }}</option>
                            <option value="3">{{ __( 'announcement.event' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $announcement_edit }}_title"">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $announcement_edit }}_content" class="col-sm-5 col-form-label">{{ __( 'announcement.content' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $announcement_edit }}_content" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="text-end">
                    <button id="{{ $announcement_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $announcement_edit }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset( 'admin/css/ckeditor/styles.css' ) }}">
<script src="{{ asset( 'admin/js/ckeditor/ckeditor.js' ) }}"></script>
<script src="{{ asset( 'admin/js/ckeditor/upload-adapter.js' ) }}"></script>

<script>
window.ckeupload_path = '{{ route( 'admin.file.ckeUpload' ) }}';
window.csrf_token = '{{ csrf_token() }}';
window.cke_element = 'announcement_edit_content';
</script>

<script src="{{ asset( 'admin/js/ckeditor/ckeditor-init.js' ) }}"></script>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let ae = '#{{ $announcement_edit }}',
            fileID = '';

        $( ae + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.announcement.index' ) }}';
        } );

        $( ae + '_submit' ).click( function() {

            resetInputValidation();

            let formData = new FormData();

            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'type', $( ae + '_type' ).val() );
            formData.append( 'title', $( ae + '_title' ).val() );
            formData.append( 'content', editor.getData() );
            formData.append( 'image', fileID );
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

        getAnnouncement();

        function getAnnouncement() {

            Dropzone.autoDiscover = false;

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.announcement.oneAnnouncement' ) }}',
                type: 'POST',
                data: {
                    'id': '{{ request( 'id' ) }}',
                    '_token': '{{ csrf_token() }}'
                },
                success: function( response ) {

                    $( ae + '_title' ).val( response.title );
                    editor.setData( response.content );
                    $( ae + '_type' ).val( response.type ).change();

                    fileID = response.path;

                    let imagePath = response.path;

                    const dropzone = new Dropzone( ae + '_photo', {
                        url: '{{ route( 'admin.file.upload' ) }}',
                        maxFiles: 1,
                        acceptedFiles: 'image/jpg,image/jpeg,image/png',
                        addRemoveLinks: true,
                        init: function() {
                            if ( imagePath ) {
                                let myDropzone = this,
                                    mockFile = { name: 'Default', size: 1024, accepted: true };

                                myDropzone.files.push( mockFile );
                                myDropzone.displayExistingFile( mockFile, imagePath );
                            }
                        },
                        removedfile: function( file, b ) {
                            fileID = null;
                            file.previewElement.remove();
                        },
                        success: function( file, response ) {
                            if ( response.status == 200 )  {
                                fileID = response.data.id;
                            }
                        }
                    } );

                    $( 'body' ).loading( 'stop' );
                }
            } );
        }
    } );
</script>