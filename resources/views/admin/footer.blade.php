    <script src="{{ asset( 'admin/js/app.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery-3.5.1.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery.dataTables.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/dataTables.bootstrap5.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery.loading.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/flatpickr-4.6.9.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/choices.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lightgallery.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/image-uploader.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lucide.min.js' ) . Helper::assetVersion() }}"></script>
    <script>
        lucide.createIcons();

        Number.prototype.toFixedDown = function(digits) {
		var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
			m = this.toString().match(re);
		return m ? parseFloat(m[1]).toFixed(digits) : this.valueOf().toFixed( 2 ).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        document.addEventListener( 'DOMContentLoaded', function() {
            $( '.form-control' ).focus( function() {
                if( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).next().text( '' );
                }
            } );
            $( '.form-select' ).focus( function() {
                if( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).next().text( '' );
                }
            } );
        } );
    </script>
</body>

</html>