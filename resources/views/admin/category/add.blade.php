<?php
$category_create = 'category_create';
?>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.general_info' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $category_create }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $category_create }}_title"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $category_create }}_description" class="col-sm-5 col-form-label">{{ __( 'product.description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $category_create }}_description" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $category_create }}_category_type" class="col-sm-5 col-form-label">{{ __( 'category.category_type' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $category_create }}_category_type">
                            <option value="1">{{ __( 'category.parent' ) }}</option>
                            <option value="2">{{ __( 'category.child' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row hidden">
                    <label for="{{ $category_create }}_parent_category" class="col-sm-5 col-form-label">{{ __( 'category.parent_category' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $category_create }}_parent_category">
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-5 col-form-label">{{ __( 'datatables.enabled' ) }}</label>
                    <div class="col-sm-7">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="{{ $category_create }}_enabled" checked></input>
                            <label class="form-check-label" for="{{ $category_create }}_enabled">
                                <small>{{ __( 'category.enable_description' ) }}</small>
                            </label>
                        </div>
                    </div>
                </div>
                @if ( 1 == 2 )
                <div class="mb-3 row">
                    <label for="{{ $category_create }}_thumbnail" class="col-sm-5 col-form-label">{{ __( 'datatables.thumbnail' ) }}</label>
                    <div class="col-sm-7">
                        <div style="position: relative; text-align: left;">
                            <img src="{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" id="{{ $category_create }}_thumbnail_preview" style="width: 50%;">
                            <div id="{{ $category_create }}_thumbnail_remove" class="mt-3 hidden">
                                <button class="btn btn-sm btn-outline-primary hidden" id="undo">Undo</button>
                                <button class="btn btn-sm btn-outline-danger" id="delete">Delete</button>
                            </div>
                        </div>
                        <input type="file" class="hidden" id="{{ $category_create }}_thumbnail" accept="image/*"></input>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                @endif
                <input type="file" class="hidden" id="{{ $category_create }}_thumbnail" accept="image/*"></input>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="text-end">
            <button id="{{ $category_create }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
            &nbsp;
            <button id="{{ $category_create }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let cc = '#{{ $category_create }}';

        $( cc + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.category.index' ) }}';
        } );
        
        $( cc + '_submit' ).click( function() {

            resetInputValidation();

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'title', $( cc + '_title' ).val() );
            formData.append( 'description', $( cc + '_description' ).val() );
            formData.append( 'category_type', $( cc + '_category_type' ).val() );
            formData.append( 'parent_category', $( cc + '_parent_category' ).val() );
            formData.append( 'enabled', $( cc + '_enabled' ).is( ':checked' ) ? 10 : 1 );
            if ( $( cc + '_thumbnail' )[0].files.length != 0 ) {
                formData.append( 'thumbnail', $( cc + '_thumbnail' )[0].files[0] );
            } else {
                formData.append( 'thumbnail', '' );
            }
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.category.createCategory' ) }}',
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
                                <div class="text-success">{{ __( 'category.category_created' ) }}</div>
                            </div>
                        </div>
                    </div>` );
                    $( window ).scrollTop( 0 );

                    setTimeout(function(){
                        $( '.alert' ).fadeTo( 250, 0.01, function() { 
                            $( this ).slideUp( 50, function() {
                                $( this ).remove();
                                window.location.href = '{{ route( 'admin.category.index' ) }}';
                            } ); 
                        } );
                    }, 2000 );
                },
                error: function( error ) {

                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( cc + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );

                        $( '.form-control.is-invalid:first' ).get( 0 ).scrollIntoView();
                    }
                }
            } );
        } );

        $( '#delete' ).click( function() {

            $( cc + '_thumbnail_preview' ).attr( 'src', '{{ asset( 'admin/img/placeholder/fff.jpg' ) }}' );
            $( cc + '_thumbnail' ).val( '' );

            let preview = $( cc + '_thumbnail_preview' ).attr( 'src' );

            if ( originalThumbnail != preview ) {
                $( '#undo' ).removeClass( 'hidden' );
            }

            $( '#delete' ).addClass( 'hidden' );
        } );

        $( '#undo' ).click( function() {

            $( cc + '_thumbnail_preview' ).attr( 'src', originalThumbnail );

            $( '#undo' ).addClass( 'hidden' );
            $( '#delete' ).removeClass( 'hidden' );
        } );

        $( cc + '_thumbnail_preview' ).click( function() {

            let that = $( this );

            $( cc + '_thumbnail' ).trigger( 'click' );
        } );

        $( cc + '_thumbnail' ).change( function() {

            let that = $( cc + '_thumbnail_preview' );

            if ( $( this ).prop('files')[0] != undefined ) {
                that.attr( 'src', URL.createObjectURL( $( this ).prop('files')[0] ) );
                $( cc + '_thumbnail_remove' ).removeClass( 'hidden' );
                $( '#delete' ).removeClass( 'hidden' );
                $( this ).next().text( '' );
            } else {
                that.attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );
            }
        } );

        let option = '';

        $( cc + '_category_type' ).change( function() {

            $( cc + '_parent_category' ).empty();            

            if ( $( this ).val() == 1 ) {

                $( cc + '_parent_category' ).parents( 'div.mb-3.row' ).addClass( 'hidden' );
            } else {

                $.ajax( {
                    url: '{{ route( 'admin.category.getCategoryStructure' ) }}',
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function( response ) {

                        traverseDown( response );

                        $( cc + '_parent_category' ).append( option );
                    }
                } );

                $( cc + '_parent_category' ).parents( 'div.mb-3.row' ).removeClass( 'hidden' );
            }
        } );

        // This block 70% was written by ChatGPT, I feel I am jobless soon 
        function traverseDown( array ) {

            array.forEach( function( item ) {

                let str = '--';
                    strLevel = str.repeat( item.level ),

                option +=
                `
                <option value="` + item.id + `">` + strLevel + ` ` + item.title + `</option>
                `;

                if ( Array.isArray( item.childrens ) ) {
                    traverseDown( item.childrens );
                }
            } );
        }
        // End
    } );
</script>