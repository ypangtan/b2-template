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
            feather.replace();

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

    $( 'th.sorting_disabled > input' ).on( 'keyup change clear', function(e) {

        var text = this.value;
        var column = $( this ).parent().data( 'cid' );
        clearTimeout( timeout );
        timeout = setTimeout( function(){
            dt_table.columns( column ).search( text ).draw();
        }, 500 );
    } );

    $( 'th.sorting_disabled > select' ).on( 'change', function() {

        var column = $( this ).parent().data( 'cid' );
        dt_table.columns( column ).search( $( this ).val() ).draw();
    } );

} );