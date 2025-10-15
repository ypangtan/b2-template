document.addEventListener( 'DOMContentLoaded', function() {

    dt_table = $( dt_table_name ).DataTable( {
        language: dt_table_config.language,
        ajax: {
            type: 'POST',
            url: dt_table_config.ajax.url,
            data: dt_table_config.ajax.data,
            dataSrc: dt_table_config.ajax.dataSrc,
            error: function( xhr, error, code ) {
                console.log(xhr);
                console.log(error);
                console.log(code);
            },
        },
        lengthMenu: dt_table_config.lengthMenu,
        processing: true,
        serverSide: true,
        rowReorder: dt_table_config.rowReorder ? dt_table_config.rowReorder : false,
        order: dt_table_config.order,
        ordering: true,
        scrollX: true,
        searchCols: dt_table_config.searchCols ? dt_table_config.searchCols : [],
        columns: dt_table_config.columns,
        columnDefs: dt_table_config.columnDefs,
        select: dt_table_config.select,
        initComplete: function() {
            $( dt_table_name + '_filter' ).remove();

            lucide.createIcons();
        },
        drawCallback: function( response ) {
            
            lucide.createIcons();

            if ( response.json.subTotal != undefined ) {
                if ( Array.isArray( response.json.subTotal ) ) {
                    $.each( response.json.subTotal, function( i, v ) {
                        $( '.dataTables_scrollFoot .subtotal' ).eq(i).html( v );
                        $( '.dataTables_scrollFoot .grandtotal' ).eq(i).html( response.json.grandTotal[i] );
                    } );
                }
            }

            window['ids'].length = 0;
            $( '.multiselect-action' ).addClass( 'hidden' );
            $( '.select-all' ).prop( 'checked', false );
            
            if( response.json.header != undefined ) {
                $.each( response.json.header, function ( i, v ) {
                    $( '#' + i ).html( v );
                } );
            }
        }
    } );

    $( dt_table_name ).on( 'page.dt length.dt order.dt search.dt', function() {
        table_no = dt_table.page.info().page * dt_table.page.info().length;
    } );

    $( dt_table_name ).on( 'preXhr.dt', function( e, settings, data ) {
        
        window['columns'].forEach( function( v, i ) {
            if ( v.type != 'default' ) {
                data[v.id] = window[v.id];
            }
        } );
    } );

    $( '.listing-filter > input' ).on( 'keydown keypress', function(e) {

        let that = $( this );
        clearTimeout( timeout );
        timeout = setTimeout( function(){
            window[that.data( 'id' )] = that.val();
            dt_table.draw();
        }, 500 );
    } );

    $( '.listing-filter > select' ).on( 'change', function() {

        let that = $( this );
        window[that.data( 'id' )] = that.val();
        dt_table.draw();
    } );
    
    let dtSelected = false;
    $( '.select-all' ).on( 'click', function() {

        multiselect = $( '.dt-multiselect' );

        if ( multiselect.length == 0 ) {
            return 0;
        }

        if ( $( this ).prop( 'checked' ) ) {
            dtSelected = true;
            $( '.dt-multiselect' ).prop( 'checked', true );
            $( '.multiselect-action' ).removeClass( 'hidden' );
            dt_table.rows().select();
        } else {
            dtSelected = false;
            $( '.multiselect-action' ).addClass( 'hidden' );
            $( '.dt-multiselect' ).prop( 'checked', false );
            dt_table.rows().deselect();
        }

        window['ids'].length = 0;
        $( '.dt-multiselect' ).each( function( i ) {
            if ( $( this ).prop( 'checked' ) ) {
                dtSelected = true;
                window['ids'].push( $( this ).data( 'id' ) );
            }
        } );
    } );

    dt_table.on( 'select', function( e, dt, type, indexes ) {

        dtSelected = true;
        let selectedCheckbox = $( $( dt_table_name + ' tbody tr' )[indexes] ).find( 'input' );
        if ( selectedCheckbox.length == 0 ) {
            return 0;
        }
        selectedCheckbox.prop( 'checked', true );
        $( '.multiselect-action' ).removeClass( 'hidden' );
        window['ids'].push( selectedCheckbox.data( 'id' ) );
    } )
    .on( 'deselect', function ( e, dt, type, indexes ) {
        dtSelected = false;
        let selectedCheckbox = $( $( dt_table_name + ' tbody tr' )[indexes] ).find( 'input' );
        if ( selectedCheckbox.length == 0 ) {
            return 0;
        }
        selectedCheckbox.prop( 'checked', false );
        window['ids'].length = 0;
        $( '.dt-multiselect' ).each( function( i ) {
            if ( $( this ).prop( 'checked' ) ) {
                dtSelected = true;
                window['ids'].push( $( this ).data( 'id' ) );
            }
        } );
        if ( !dtSelected ) {
            $( '.multiselect-action' ).addClass( 'hidden' );
        }
    } );
    
    $( '.dt-export' ).click( function() {

        let sort = dt_table.order(),
            url = 'order[0][column]='+( sort[0] ? sort[0][0] : 1 )+'&order[0][dir]='+( sort[0] ? sort[0][1] : 'DESC' );
        
        window['columns'].forEach( function( v, i ) {
            if ( v.type != 'default' ) {
                if ( v.type == 'checkbox' ) {
                    let checkboxValue = [];
                    $.each( $( '*[data-id="trxtype"]' ), function( i, v ) {
                        if ( $( v ).is( ':checked' ) ) {
                            checkboxValue.push( $( v ).val() );
                        }
                    } );
                    url += ( '&' + v.id + '=' + checkboxValue.join( ',' ) );
                } else {
                    url += ( '&' + v.id + '=' + ( $( '#' + v.id ).val() ?? '' ) );
                }
            }
        } );

        const urlParams = new URL( exportPath );
        let newExportPath = urlParams.origin + urlParams.pathname;

        if ( urlParams.search != '' ) {
            url += urlParams.search.replace( '?', '&' );
        }

        window.open(newExportPath + '?' + url, '_blank');
    } );

    // Reorder
    $( dt_table_name ).on( 'row-reorder.dt', function( e, diff, edit ) {
        const updates = diff.map(change => {
            const id = $(change.node).find('.dt-reorder').data('id');
            const position = change.newPosition;

            return { id, position };
        });

        if ( updates.length ) {
            $.ajax( {
                url: reorderPath,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    updates: updates
                },
                success: function( res ) {
                    dt_table.draw( false );
                }
            } );
        }
    });
    
} );