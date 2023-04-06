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
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_sku"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_title"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
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
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_regular_price"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_taxable" class="col-sm-5 col-form-label">{{ __( 'product.taxable' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_taxable"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_promo_price" class="col-sm-5 col-form-label">{{ __( 'product.promo_price' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_price"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_promo_price_from" class="col-sm-5 col-form-label">{{ __( 'product.promo_price_from' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_price_from"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_promo_price_to" class="col-sm-5 col-form-label">{{ __( 'product.promo_price_to' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_promo_price_to"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'product.stock' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_quantity" class="col-sm-5 col-form-label">{{ __( 'product.quantity' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_quantity"></input>
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
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_friendly_url"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $product_create }}_meta_title" class="col-sm-5 col-form-label">{{ __( 'template.meta_title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $product_create }}_meta_title"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
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
            <button id="{{ $product_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
            &nbsp;
            <button id="{{ $product_create }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        $( '.input-images' ).imageUploader( {
            label: '{!! __( 'template.drag_n_drop' ) !!}',
            extensions: [ '.jpg', '.jpeg', '.png', '.gif', '.svg', '.mp4', '.MP4' ],
            mimes: [ 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'video/mp4', 'video/mp4' ],
            maxSize: [ 64 * 1024 * 1024 ],
        } );
    } );
</script>