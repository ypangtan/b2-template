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
        order: dt_table_config.order,
        ordering: true,
        scrollX: true,
        searchCols: dt_table_config.searchCols ? dt_table_config.searchCols : [],
        columns: dt_table_config.columns,
        columnDefs: dt_table_config.columnDefs,
        initComplete: function() {
            $( dt_table_name + '_filter' ).remove();
        },
        drawCallback: function( response ) {
            
            lucide.createIcons();

            if( response.json.subTotal != undefined ) {
                if( Array.isArray( response.json.subTotal ) ) {
                    $.each( response.json.subTotal, function( i, v ) {
                        $( '.dataTables_scrollFoot .subtotal' ).eq(i).html( v );
                        $( '.dataTables_scrollFoot .grandtotal' ).eq(i).html( response.json.grandTotal[i] );
                    } );
                }
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
            console.log(window[that.data( 'id' )]);
            dt_table.draw();
        }, 500 );
    } );

    $( '.listing-filter > select' ).on( 'change', function() {

        let that = $( this );
        window[that.data( 'id' )] = that.val();
        dt_table.draw();
    } );

} );