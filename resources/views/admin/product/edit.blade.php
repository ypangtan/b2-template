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
                            <option value="0">{{ __( 'datatables.no' ) }}</option>
                            <option value="1">{{ __( 'datatables.yes' ) }}</option>
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
                        <input type="text" class="form-control form-control-sm" id="{{ $product_edit }}_friendly_url">
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
                <div id="jstree_category">
                    <ul></ul>
                </div>
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
                <textarea class="form-control" id="{{ $product_edit }}_description"></textarea>
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
                <div class="images" id="{{ $product_edit }}_images"></div>
                <div class="invalid-feedback"></div>
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

<link rel="stylesheet" href="{{ asset( 'admin/css/ckeditor/styles.css' ) }}">
<script src="{{ asset( 'admin/js/ckeditor/ckeditor.js' ) }}"></script>
<script src="{{ asset( 'admin/js/ckeditor/upload-adapter.js' ) }}"></script>

<script>
window.ckeupload_path = '{{ route( 'admin.product.ckeupload' ) }}';
window.csrf_token = '{{ csrf_token() }}';
window.cke_element = 'product_edit_description';
</script>

<script src="{{ asset( 'admin/js/ckeditor/ckeditor-init.js' ) }}"></script>

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

            if ( $( this ).val() == 1 ) {
                $( '#promo_section' ).removeClass( 'hidden' );
            } else {
                $( '#promo_section' ).addClass( 'hidden' );
            }
        } );

        $( pe + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.product.index' ) }}';
        } );

        $( pe + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'sku', $( pe + '_sku' ).val() );
            formData.append( 'title', $( pe + '_title' ).val() );
            formData.append( 'short_description', $( pe + '_short_description' ).val() );
            formData.append( 'description', editor.getData() );
            formData.append( 'regular_price', $( pe + '_regular_price' ).val() );
            formData.append( 'taxable', $( pe + '_taxable' ).val() );
            formData.append( 'enable_promotion', $( pe + '_enable_promotion' ).val() );
            formData.append( 'promo_price', $( pe + '_promo_price' ).val() );
            formData.append( 'promo_date_from', $( pe + '_promo_date_from' ).val() );
            formData.append( 'promo_date_to', $( pe + '_promo_date_to' ).val() );
            formData.append( 'quantity', $( pe + '_quantity' ).val() );

            $.each( $( '.images input[name="preloaded[]"]' ), function( i, v ) {
                formData.append( 'preloaded[]', v.value );
            } );

            $.each( $( '.images input[name="images[]"]' )[0].files, function( i, v ) {
                formData.append( 'images[]', v );
            } );

            formData.append( 'friendly_url', $( pe + '_friendly_url' ).val() );
            formData.append( 'meta_title', $( pe + '_meta_title' ).val() );
            formData.append( 'meta_description', $( pe + '_meta_description' ).val() );

            formData.append( 'categories', JSON.stringify( jsTree.jstree().get_selected() ) );

            // let a = jsTree.jstree();
            // console.log( a.get_selected() );
            // console.log( a.get_top_selected() );
            // console.log( a.get_bottom_selected() );

            // return 0;

            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.product.updateProduct' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.product.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( pe + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );

                        $( '.form-control.is-invalid:first' ).get( 0 ).scrollIntoView();
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
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

                    let product = response.product;

                    $( pe + '_sku' ).val( product.sku );
                    $( pe + '_title' ).val( product.title );
                    $( pe + '_short_description' ).val( product.short_description );
                    editor.setData( product.description );
                    $( pe + '_regular_price' ).val( product.product_prices[0].regular_price );
                    // $( pe + '_taxable' ).val( product.taxable );
                    $( pe + '_enable_promotion' ).val( product.product_prices[0].promo_enabled ).change();
                    if ( product.product_prices[0].promo_enabled ) {
                        $( pe + '_promo_price' ).val( product.product_prices[0].promo_price );
                        promoDateFrom.setDate( product.product_prices[0].promo_date_from );
                        promoDateTo.setDate( product.product_prices[0].promo_date_to );
                    }
                    $( pe + '_quantity' ).val( product.product_inventory.quantity );
                    $( pe + '_description' ).val( product.description );
                    $( pe + '_friendly_url' ).val( product.url_slug );

                    product.metadata.map( function( v, i ) {
                        $( pe + '_' + v.key ).val( v.value );
                    } );

                    let preloaded = [];
                    product.product_images.map( function( v, i ) {
                        preloaded.push( {
                            id: v.id,
                            src: v.path,
                            type: v.type,
                        } );
                    } );

                    $( '.images' ).imageUploader( {
                        preloaded,
                        label: '{!! __( 'template.drag_n_drop' ) !!}',
                        extensions: [ '.jpg', '.jpeg', '.png' ],
                        mimes: [ 'image/jpeg', 'image/png' ],
                        maxSize: [ 64 * 1024 * 1024 ],
                    } );

                    traverseDown( response.categories );

                    jsTree = $( '#jstree_category' );
                    jsTree.jstree( {
                        plugins: [ 'wholerow', 'checkbox' ],
                        checkbox: {
                            three_state: false,
                        },
                    } ).on('changed.jstree', function (e, data) {
                        console.log( data );
                    } );

                    jsTree.jstree( 'open_all' );

                    product.product_categories.map( function( v, i ) {

                        jsTree.jstree( 'check_node', 'child_' + v.category_id );
                        
                        // if ( v.is_child ) {
                        //     jsTree.jstree( 'check_node', 'child_' + v.category_id );
                        // }
                        // jsTree.jstree( 'open_node', 'child_' + v.category_id, function( e, d ) {
                        //     if( e.parents.length ){
                        //         jsTree.jstree( 'open_node', e.parent );
                        //     };
                        // });
                    } );

                    $( 'body' ).loading( 'stop' );
                }
            } );
        }

        function traverseDown( array ) {

            array.forEach( function( item ) {

                console.log( item );

                let structure1 =
                `
                <li id="child_` + item.id + `">` + item.title + ( item.childrens ? `<ul></ul>` : '' ) + `</li>
                `;

                if ( item.parent_id ) {
                    $( '#jstree_category #child_' + item.parent_id + ' > ul' ).append( structure1 );
                } else {
                    $( '#jstree_category > ul' ).append( structure1 );
                }

                if ( Array.isArray( item.childrens ) ) {
                    traverseDown( item.childrens );
                }
            } );
        }

    } );
</script>