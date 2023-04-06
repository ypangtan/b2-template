<?php
$product_edit = 'product_edit';
?>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.general_info' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $product_edit }}_sku" class="col-sm-5 col-form-label">{{ __( 'product.sku' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_sku">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_edit }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-0 row">
                    <label for="{{ $product_edit }}_short_description" class="col-sm-5 col-form-label">{{ __( 'template.short_description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $product_edit }}_short_description" rows="10"></textarea>
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
                    <label for="{{ $product_edit }}_regular_price" class="col-sm-5 col-form-label">{{ __( 'product.regular_price' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_regular_price">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_edit }}_taxable" class="col-sm-5 col-form-label">{{ __( 'product.taxable' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $product_edit }}_taxable">
                            <option value="yes">{{ __( 'datatables.yes' ) }}</option>
                            <option value="no">{{ __( 'datatables.no' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_edit }}_enable_promotion" class="col-sm-5 col-form-label">{{ __( 'product.enable_promotion' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $product_edit }}_enable_promotion">
                            <option value="no">{{ __( 'datatables.no' ) }}</option>
                            <option value="yes">{{ __( 'datatables.yes' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div id="promo_section" class="hidden mt-3">
                    <div class="mb-3 row">
                        <label for="{{ $product_edit }}_promo_price" class="col-sm-5 col-form-label">{{ __( 'product.promo_price' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_promo_price">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="{{ $product_edit }}_promo_date_from" class="col-sm-5 col-form-label">{{ __( 'product.promo_date_from' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_promo_date_from">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-0 row">
                        <label for="{{ $product_edit }}_promo_date_to" class="col-sm-5 col-form-label">{{ __( 'product.promo_date_to' ) }}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_promo_date_to">
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
                    <label for="{{ $product_edit }}_quantity" class="col-sm-5 col-form-label">{{ __( 'product.quantity' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_quantity">
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
                    <label for="{{ $product_edit }}_friendly_url" class="col-sm-5 col-form-label">{{ __( 'template.friendly_url' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_friendly_url" placeholder="{{ __( 'template.optional' ) }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_edit }}_meta_title" class="col-sm-5 col-form-label">{{ __( 'template.meta_title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_meta_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-0 row">
                    <label for="{{ $product_edit }}_meta_description" class="col-sm-5 col-form-label">{{ __( 'template.meta_description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $product_edit }}_meta_description" rows="10"></textarea>
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
                <textarea class="w-100" rows="10"></textarea>
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
                <div class="input-images"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-12 col-xl-12">
        <div class="text-end">
            <button id="{{ $product_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
            &nbsp;
            <button id="{{ $product_edit }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let pe = '#{{ $product_edit }}';

        getProduct();

        let promoDateFrom = $( pe + '_promo_date_from' ).flatpickr( {
            disableMobile: true,
            enableTime: true,
        } );

        let promoDateTo = $( pe + '_promo_date_to' ).flatpickr( {
            disableMobile: true,
            enableTime: true,
        } );

        $( '.input-images' ).imageUploader( {
            label: '{!! __( 'template.drag_n_drop' ) !!}',
            extensions: [ '.jpg', '.jpeg', '.png', '.gif', '.svg', '.mp4', '.MP4' ],
            mimes: [ 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'video/mp4', 'video/mp4' ],
            maxSize: [ 64 * 1024 * 1024 ],
        } );

        $( pe + '_enable_promotion' ).change( function() {

            if ( $( this ).val() == 'yes' ) {
                $( '#promo_section' ).removeClass( 'hidden' );
            } else {
                $( '#promo_section' ).addClass( 'hidden' );
            }
        } );

        $( pe + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.product.index' ) }}';
        } );

        $( pe + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'sku', $( pe + '_sku' ).val() );
            formData.append( 'title', $( pe + '_title' ).val() );
            formData.append( 'short_description', $( pe + '_short_description' ).val() );
            formData.append( 'regular_price', $( pe + '_regular_price' ).val() );
            formData.append( 'taxable', $( pe + '_taxable' ).val() );
            formData.append( 'enable_promotion', $( pe + '_enable_promotion' ).val() );
            formData.append( 'promo_price', $( pe + '_promo_price' ).val() );
            formData.append( 'promo_date_from', $( pe + '_promo_date_from' ).val() );
            formData.append( 'promo_date_to', $( pe + '_promo_date_to' ).val() );

            formData.append( '_token', '{{ csrf_token() }}' );
        } );

        function getProduct() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.product.oneProduct' ) }}',
                type: 'POST',
                data: {
                    'id': '{{ request( 'id' ) }}',
                    '_token': '{{ csrf_token() }}'
                },
                success: function( response ) {

                    $( pe + '_sku' ).val( response.sku );
                    $( pe + '_title' ).val( response.title );
                    $( pe + '_short_description' ).val( response.short_description );
                    $( pe + '_regular_price' ).val( response.product_prices[0].regular_price );
                    // $( pe + '_taxable' ).val( response.taxable );
                    $( pe + '_enable_promotion' ).val( response.product_prices[0].promo_enabled ).change();
                    if ( response.product_prices[0].promo_enabled == 'yes' ) {
                        $( pe + '_promo_price' ).val( response.product_prices[0].promo_price );
                        promoDateFrom.setDate( response.product_prices[0].promo_date_from );
                        promoDateTo.setDate( response.product_prices[0].promo_date_to );
                    }
                    $( pe + '_quantity' ).val( response.product_inventory.quantity );
                    $( pe + '_description' ).val( response.description );
                    $( pe + '_friendly_url' ).val( response.url_slug );

                    $( 'body' ).loading( 'stop' );
                }
            } );
        }

    } );
</script>