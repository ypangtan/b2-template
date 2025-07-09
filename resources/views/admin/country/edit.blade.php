<?php
$country_edit = 'country_edit';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">

                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_country_name" class="col-sm-5 col-form-label">{{ __( 'country.country_name' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_country_name">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_currency_name" class="col-sm-5 col-form-label">{{ __( 'country.currency_name' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_currency_name">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_currency_symbol" class="col-sm-5 col-form-label">{{ __( 'country.currency_symbol' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_currency_symbol">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_iso_currency" class="col-sm-5 col-form-label">{{ __( 'country.iso_currency' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_iso_currency">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_iso_alpha2_code" class="col-sm-5 col-form-label">{{ __( 'country.iso_alpha2_code' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_iso_alpha2_code">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_iso_alpha3_code" class="col-sm-5 col-form-label">{{ __( 'country.iso_alpha3_code' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_iso_alpha3_code">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $country_edit }}_call_code" class="col-sm-5 col-form-label">{{ __( 'country.call_code' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $country_edit }}_call_code">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="mb-1">{{ __( 'country.iamge' ) }}</label>
                    <div class="dropzone" id="{{ $country_edit }}_image" style="min-height: 0px;">
                        <div class="dz-message needsclick">
                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">{{ __( 'template.drop_file_or_click_to_upload' ) }}</h3>
                        </div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3 row">
                    <label class="mb-1">{{ __( 'country.icon' ) }}</label>
                    <div class="dropzone" id="{{ $country_edit }}_icon" style="min-height: 0px;">
                        <div class="dz-message needsclick">
                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">{{ __( 'template.drop_file_or_click_to_upload' ) }}</h3>
                        </div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="text-end">
                    <button id="{{ $country_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
                    &nbsp;
                    <button id="{{ $country_edit }}_submit" type="button" class="btn btn-sm btn-primary">{{ __( 'template.save_changes' ) }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        getCountry();

        let ue = '#{{ $country_edit }}',
            fileID = '',
            file2ID = '';

        Dropzone.autoDiscover = false;

        $( ue + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.country.index' ) }}';
        } );

        $( ue + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'country_name', $( ue + '_country_name' ).val() );
            formData.append( 'currency_name', $( ue + '_currency_name' ).val() );
            formData.append( 'currency_symbol', $( ue + '_currency_symbol' ).val() );
            formData.append( 'iso_currency', $( ue + '_iso_currency' ).val() );
            formData.append( 'iso_alpha2_code', $( ue + '_iso_alpha2_code' ).val() );
            formData.append( 'iso_alpha3_code', $( ue + '_iso_alpha3_code' ).val() );
            formData.append( 'call_code', $( ue + '_call_code' ).val() );
            formData.append( 'country_image', fileID );
            formData.append( 'country_icon', file2ID );
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.country.updateCountry' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.country.index' ) }}';
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

        function getCountry() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.country.oneCountry' ) }}',
                type: 'POST',
                data: {
                    id: '{{ request( 'id' ) }}',
                    _token: '{{ csrf_token() }}',
                },
                success: function( response ) {
                    
                    $( ue + '_country_name' ).val( response.country_name );
                    $( ue + '_currency_name' ).val( response.currency_name );
                    $( ue + '_currency_symbol' ).val( response.currency_symbol );
                    $( ue + '_iso_currency' ).val( response.iso_currency );
                    $( ue + '_iso_alpha2_code' ).val( response.iso_alpha2_code );
                    $( ue + '_iso_alpha2_code' ).val( response.iso_alpha2_code );
                    $( ue + '_call_code' ).val( response.call_code );

                    fileID = response.image_path;
                    file2ID = response.image_icon;
                    let imagePath = response.image_path;
                    let iconPath = response.image_icon;

                    const dropzone = new Dropzone( ue + '_image', { 
                        url: '{{ route( 'admin.file.countryImageUpload' ) }}',
                        maxFiles: 1,
                        acceptedFiles: 'image/jpg,image/jpeg,image/png',
                        addRemoveLinks: true,
                        init: function() {
                            this.on("addedfile", function (file) {
                                if (this.files.length > 1) {
                                    this.removeFile(this.files[0]);
                                }
                            });
                            if ( imagePath ) {
                                let myDropzone = this,
                                    mockFile = { name: 'Default', size: 1024, accepted: true };

                                myDropzone.files.push( mockFile );
                                myDropzone.displayExistingFile( mockFile, imagePath );
                            }
                        },
                        removedfile: function( file ) {
                            fileID = null;
                            file.previewElement.remove();
                        },
                        success: function( file, response ) {
                            if ( response.status == 200 )  {
                                fileID = response.url;
                            }
                        }
                    } );

                    const dropzone2 = new Dropzone( ue + '_icon', { 
                        url: '{{ route( 'admin.file.countryIconUpload' ) }}',
                        maxFiles: 1,
                        acceptedFiles: 'image/jpeg,image/jpg,image/png,image/svg+xml',
                        addRemoveLinks: true,
                        init: function() {
                            this.on("addedfile", function (file) {
                                if (this.files.length > 1) {
                                    this.removeFile(this.files[0]);
                                }
                            });
                            if ( iconPath ) {
                                let myDropzone = this,
                                    mockFile = { name: 'Default', size: 1024, accepted: true };

                                myDropzone.files.push( mockFile );
                                myDropzone.displayExistingFile( mockFile, imagePath );
                            }
                        },
                        removedfile: function( file ) {
                            file2ID = null;
                            file.previewElement.remove();
                        },
                        success: function( file, response ) {
                            if ( response.status == 200 )  {
                                file2ID = response.url;
                            }
                        }
                    } );

                    $( 'body' ).loading( 'stop' );
                },
            } );
        }
    } );
</script>