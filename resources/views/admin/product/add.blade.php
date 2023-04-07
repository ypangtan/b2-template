<?php
$product_create = 'product_create';
?>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.general_info' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_sku" class="col-sm-5 col-form-label">{{ __( 'product.sku' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_sku">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-0 row">
                    <label for="{{ $product_create }}_short_description" class="col-sm-5 col-form-label">{{ __( 'template.short_description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $product_create }}_short_description" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'product.pricing' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_regular_price" class="col-sm-5 col-form-label">{{ __( 'product.regular_price' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_regular_price">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_taxable" class="col-sm-5 col-form-label">{{ __( 'product.taxable' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $product_create }}_taxable">
                            <option value="yes">{{ __( 'datatables.yes' ) }}</option>
                            <option value="no">{{ __( 'datatables.no' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_enable_promotion" class="col-sm-5 col-form-label">{{ __( 'product.enable_promotion' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $product_create }}_enable_promotion">
                            <option value="no">{{ __( 'datatables.no' ) }}</option>
                            <option value="yes">{{ __( 'datatables.yes' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div id="promo_section" class="hidden mt-3">
                    <div class="mb-3 row">
                        <label for="{{ $product_create }}_promo_price" class="col-sm-5 col-form-label">{{ __( 'product.promo_price' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_price">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="{{ $product_create }}_promo_date_from" class="col-sm-5 col-form-label">{{ __( 'product.promo_date_from' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_date_from">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-0 row">
                        <label for="{{ $product_create }}_promo_date_to" class="col-sm-5 col-form-label">{{ __( 'product.promo_date_to' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_date_to">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'product.stock' ) }}</h5>
                <hr>
                <div class="mb-0 row">
                    <label for="{{ $product_create }}_quantity" class="col-sm-5 col-form-label">{{ __( 'product.quantity' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_quantity">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.seo' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_friendly_url" class="col-sm-5 col-form-label">{{ __( 'template.friendly_url' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_friendly_url">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_meta_title" class="col-sm-5 col-form-label">{{ __( 'template.meta_title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_meta_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-0 row">
                    <label for="{{ $product_create }}_meta_description" class="col-sm-5 col-form-label">{{ __( 'template.meta_description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $product_create }}_meta_description" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.category' ) }}</h5>
                <hr>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.description' ) }}</h5>
                <hr>
                <textarea class="form-control" id="{{ $product_create }}_description"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.gallery' ) }}</h5>
                <hr>
                <div class="images" id="{{ $product_create }}_images"></div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-12 col-xl-12">
        <div class="text-end">
            <button id="{{ $product_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
            &nbsp;
            <button id="{{ $product_create }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset( 'admin/css/ckeditor/styles.css' ) }}">
<script src="{{ asset( 'admin/js/ckeditor/ckeditor.js' ) }}"></script>
<script src="{{ asset( 'admin/js/ckeditor/upload-adapter.js' ) }}"></script>

<script>
window.ckeupload_path = '{{ route( 'admin.product.ckeupload' ) }}';
window.csrf_token = '{{ csrf_token() }}';
window.cke_element = 'product_create_description';
</script>

<script src="{{ asset( 'admin/js/ckeditor/ckeditor-init.js' ) }}"></script>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let pc = '#{{ $product_create }}';

        $( pc + '_promo_date_from' ).flatpickr( {
            disableMobile: true,
            enableTime: true,
        } );

        $( pc + '_promo_date_to' ).flatpickr( {
            disableMobile: true,
            enableTime: true,
        } );

        $( '.images' ).imageUploader( {
            label: '{!! __( 'template.drag_n_drop' ) !!}',
            extensions: [ '.jpg', '.jpeg', '.png' ],
            mimes: [ 'image/jpeg', 'image/png' ],
            maxSize: [ 64 * 1024 * 1024 ],
        } );

        $( pc + '_enable_promotion' ).change( function() {

            if ( $( this ).val() == 'yes' ) {
                $( '#promo_section' ).removeClass( 'hidden' );
            } else {
                $( '#promo_section' ).addClass( 'hidden' );
            }
        } );

        $( pc + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.product.index' ) }}';
        } );

        $( pc + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'sku', $( pc + '_sku' ).val() );
            formData.append( 'title', $( pc + '_title' ).val() );
            formData.append( 'short_description', $( pc + '_short_description' ).val() );
            formData.append( 'description', editor.getData() );
            formData.append( 'regular_price', $( pc + '_regular_price' ).val() );
            formData.append( 'taxable', $( pc + '_taxable' ).val() );
            formData.append( 'enable_promotion', $( pc + '_enable_promotion' ).val() );
            formData.append( 'promo_price', $( pc + '_promo_price' ).val() );
            formData.append( 'promo_date_from', $( pc + '_promo_date_from' ).val() );
            formData.append( 'promo_date_to', $( pc + '_promo_date_to' ).val() );
            formData.append( 'quantity', $( pc + '_quantity' ).val() );

            $.each( $( '.images input[name="images[]"]' )[0].files, function( i, file ) {
                formData.append('images[]', file);
            } );

            formData.append( 'friendly_url', $( pc + '_friendly_url' ).val() );
            formData.append( 'meta_title', $( pc + '_meta_title' ).val() );
            formData.append( 'meta_description', $( pc + '_meta_description' ).val() );

            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.product.createProduct' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    
                    $( 'body' ).loading( 'stop' );

                    $( 'main.page-content' ).prepend( `
                    <div class="alert border-0 border-success border-start border-4 bg-light-success fade show py-2">
                        <div class="d-flex align-items-center">
                            <div class="fs-3 text-success"><i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="ms-3">
                                <div class="text-success">{{ __( 'product.product_created' ) }}</div>
                            </div>
                        </div>
                    </div>` );
                    $( window ).scrollTop( 0 );

                    setTimeout(function(){
                        $( '.alert' ).fadeTo( 250, 0.01, function() { 
                            $( this ).slideUp( 50, function() {
                                $( this ).remove();
                                window.location.href = '{{ route( 'admin.product.index' ) }}';
                            } ); 
                        } );
                    }, 2000 );
                },
                error: function( error ) {

                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( pc + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );

                        $( '.form-control.is-invalid:first' ).get( 0 ).scrollIntoView();
                    }
                }
            } );
        } );

    } );
</script>