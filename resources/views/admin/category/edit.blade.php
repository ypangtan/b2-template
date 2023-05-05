<?php
$category_edit = 'category_edit';
?>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __( 'template.general_info' ) }}</h5>
                <hr>
                <div class="mb-3 row">
                    <label for="{{ $category_edit }}_title" class="col-sm-5 col-form-label">{{ __( 'datatables.title' ) }}</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="{{ $category_edit }}_title">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $category_edit }}_description" class="col-sm-5 col-form-label">{{ __( 'template.description' ) }}</label>
                    <div class="col-sm-7">
                        <textarea class="form-control form-control-sm" id="{{ $category_edit }}_description" rows="10"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="{{ $category_edit }}_category_type" class="col-sm-5 col-form-label">{{ __( 'category.category_type' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $category_edit }}_category_type">
                            <option value="1">{{ __( 'category.parent' ) }}</option>
                            <option value="2">{{ __( 'category.child' ) }}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row hidden">
                    <label for="{{ $category_edit }}_parent_category" class="col-sm-5 col-form-label">{{ __( 'category.parent_category' ) }}</label>
                    <div class="col-sm-7">
                        <select class="form-control form-control-sm" id="{{ $category_edit }}_parent_category">
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-5 col-form-label">{{ __( 'datatables.enabled' ) }}</label>
                    <div class="col-sm-7">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="{{ $category_edit }}_enabled">
                            <label class="form-check-label" for="{{ $category_edit }}_enabled">
                                <small>{{ __( 'category.enable_description' ) }}</small>
                            </label>
                        </div>
                    </div>
                </div>
                @if ( 1 == 2 )
                <div class="mb-3 row">
                    <label for="{{ $category_edit }}_thumbnail" class="col-sm-5 col-form-label">{{ __( 'datatables.thumbnail' ) }}</label>
                    <div class="col-sm-7">
                        <div style="position: relative; text-align: left;">
                            <img src="{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" id="{{ $category_edit }}_thumbnail_preview" style="width: 50%;">
                            <div id="{{ $category_edit }}_thumbnail_remove" class="mt-3 hidden">
                                <button class="btn btn-sm btn-outline-primary hidden" id="undo">Undo</button>
                                <button class="btn btn-sm btn-outline-danger" id="delete">Delete</button>
                            </div>
                        </div>
                        <input type="file" class="hidden" id="{{ $category_edit }}_thumbnail" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                @endif
                <input type="file" class="hidden" id="{{ $category_edit }}_thumbnail" accept="image/*">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-6">
        <div class="text-end">
            <button id="{{ $category_edit }}_cancel" type="button" class="btn btn-sm btn-outline-secondary">{{ __( 'template.cancel' ) }}</button>
            &nbsp;
            <button id="{{ $category_edit }}_submit" type="button" class="btn btn-sm btn-success">{{ __( 'template.save_changes' ) }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener( 'DOMContentLoaded', function() {

        let originalThumbnail = '',
            ce = '#{{ $category_edit }}';

        getCategory();

        $( ce + '_cancel' ).click( function() {
            window.location.href = '{{ route( 'admin.module_parent.category.index' ) }}';
        } );

        $( ce + '_submit' ).click( function() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            let formData = new FormData();
            formData.append( 'id', '{{ request( 'id' ) }}' );
            formData.append( 'title', $( ce + '_title' ).val() );
            formData.append( 'description', $( ce + '_description' ).val() );
            formData.append( 'category_type', $( ce + '_category_type' ).val() );
            formData.append( 'parent_category', $( ce + '_parent_category' ).val() );
            formData.append( 'enabled', $( ce + '_enabled' ).is( ':checked' ) ? 10 : 1 );
            if ( $( ce + '_thumbnail' )[0].files.length != 0 ) {
                formData.append( 'thumbnail', $( ce + '_thumbnail' )[0].files[0] );
            } else {
                formData.append( 'thumbnail', '' );

                let preview = $( ce + '_thumbnail_preview' ).attr( 'src' );
                if ( originalThumbnail != preview ) {
                    formData.append( 'thumbnail_removed', 1 );
                }
            }
            formData.append( '_token', '{{ csrf_token() }}' );

            $.ajax( {
                url: '{{ route( 'admin.category.updateCategory' ) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $( 'body' ).loading( 'stop' );
                    $( '#modal_success .caption-text' ).html( response.message );
                    modalSuccess.toggle();

                    document.getElementById( 'modal_success' ).addEventListener( 'hidden.bs.modal', function (event) {
                        window.location.href = '{{ route( 'admin.module_parent.category.index' ) }}';
                    } );
                },
                error: function( error ) {
                    $( 'body' ).loading( 'stop' );

                    if ( error.status === 422 ) {
                        let errors = error.responseJSON.errors;
                        $.each( errors, function( key, value ) {
                            $( ce + '_' + key ).addClass( 'is-invalid' ).next().text( value );
                        } );

                        $( '.form-control.is-invalid:first' ).get( 0 ).scrollIntoView();
                    } else {
                        $( '#modal_danger .caption-text' ).html( error.responseJSON.message );
                        modalDanger.toggle();       
                    }
                }
            } );
        } );

        $( '#delete' ).click( function() {

            $( ce + '_thumbnail_preview' ).attr( 'src', '{{ asset( 'admin/img/placeholder/fff.jpg' ) }}' );
            $( ce + '_thumbnail' ).val( '' );

            let preview = $( ce + '_thumbnail_preview' ).attr( 'src' );

            if ( originalThumbnail != preview ) {
                $( '#undo' ).removeClass( 'hidden' );
            }

            $( '#delete' ).addClass( 'hidden' );
        } );

        $( '#undo' ).click( function() {

            $( ce + '_thumbnail_preview' ).attr( 'src', originalThumbnail );

            $( '#undo' ).addClass( 'hidden' );
            $( '#delete' ).removeClass( 'hidden' );
        } );

        $( ce + '_thumbnail_preview' ).click( function() {

            let that = $( this );

            $( ce + '_thumbnail' ).trigger( 'click' );
        } );

        $( ce + '_thumbnail' ).change( function() {

            let that = $( ce + '_thumbnail_preview' );

            if ( $( this ).prop('files')[0] != undefined ) {
                that.attr( 'src', URL.createObjectURL( $( this ).prop('files')[0] ) );
                $( ce + '_thumbnail_remove' ).removeClass( 'hidden' );
                $( '#delete' ).removeClass( 'hidden' );
                $( this ).next().text( '' );
            } else {
                that.attr( 'src', "{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" );
            }
        } );

        $( ce + '_enabled' ).change( function() {
            if ( $( this ).is( ':checked' ) ) {
                $( this ).next().children( 'small' ).html( '{{ __( 'category.enable_description' ) }}' );
            } else {
                $( this ).next().children( 'small' ).html( '{{ __( 'category.disable_description' ) }}' );
            }
        } );

        let option = '';

        $( ce + '_category_type' ).change( function() {

            $( ce + '_parent_category' ).empty();

            if ( $( this ).val() == 1 ) {

                $( ce + '_parent_category' ).parents( 'div.mb-3.row' ).addClass( 'hidden' );
            } else {

                $.ajax( {
                    url: '{{ route( 'admin.category.getCategoryStructure' ) }}',
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function( response ) {

                        traverseDown( response );

                        $( ce + '_parent_category' ).append( option );
                    }
                } );

                $( ce + '_parent_category' ).parents( 'div.mb-3.row' ).removeClass( 'hidden' );
            }
        } );

        function getCategory() {

            $( 'body' ).loading( {
                message: '{{ __( 'template.loading' ) }}'
            } );

            $.ajax( {
                url: '{{ route( 'admin.category.oneCategory' ) }}',
                type: 'POST',
                data: {
                    id: '{{ request( 'id' ) }}',
                    '_token': '{{ csrf_token() }}'
                },
                success: function( response ) {

                    $( ce + '_title' ).val( response.title );
                    $( ce + '_description' ).val( response.description );
                    $( ce + '_category_type' ).val( response.type );
                    if ( response.type == 2 ) {
                        let parentID = response.parent_id;
                        $( ce + '_parent_category' ).parents( 'div.mb-3.row' ).removeClass( 'hidden' );
                        $.ajax( {
                            url: '{{ route( 'admin.category.getCategoryStructure' ) }}',
                            type: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function( response ) {

                                traverseDown( response );

                                $( ce + '_parent_category' ).append( option );

                                $( ce + '_parent_category' ).val( parentID );
                            }
                        } );

                        $( ce + '_parent_category' ).parents( 'div.mb-3.row' ).removeClass( 'hidden' );
                    }
                    if ( response.status == 10 ) {
                        $( ce + '_enabled' ).prop( 'checked', true );
                    }

                    originalThumbnail = response.path;
                    if ( response.path ) {
                        $( ce + '_thumbnail_preview' ).attr( 'src', originalThumbnail );
                        $( ce + '_thumbnail_remove' ).removeClass( 'hidden' );
                    } else {
                        originalThumbnail = '{{ asset( 'admin/img/placeholder/fff.jpg' ) }}';
                        $( ce + '_thumbnail_preview' ).attr( 'src', '{{ asset( 'admin/img/placeholder/fff.jpg' ) }}' );
                    }

                    $( 'body' ).loading( 'stop' );
                }
            } );
        }

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
    } );
</script>