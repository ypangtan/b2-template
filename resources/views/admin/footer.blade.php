    <script src="{{ asset( 'admin/js/bootstrap.bundle.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery-3.5.1.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery.dataTables.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/dataTables.bootstrap5.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery.loading.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/simplebar/js/simplebar.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/metismenu/js/metisMenu.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/pace.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/image-uploader.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lucide.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/flatpickr-4.6.13.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lightgallery.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script src="{{ asset( 'admin/js/select2.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    
    <script>
        lucide.createIcons();

        Number.prototype.toFixedDown = function(digits) {
		var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
			m = this.toString().match(re);
		return m ? parseFloat(m[1]).toFixed(digits) : this.valueOf().toFixed( 2 ).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        let modalConfirmation = new bootstrap.Modal( document.getElementById( 'modal_confirmation' ) ),
            modalSuccess = new bootstrap.Modal( document.getElementById( 'modal_success' ) ),
            modalDanger = new bootstrap.Modal( document.getElementById( 'modal_danger' ) );

        document.addEventListener( 'DOMContentLoaded', function() {

            let parents = [];
            let menus = [];

            // and when you show it, move it to the body
            $( '.dataTables_scrollBody' ).on( 'show.bs.dropdown', function( e ) {

                let target = $( e.target );

                // save the parent
                parents.push( target.parent() );

                // grab the menu
                let dropdownMenu = target.next();

                // save the menu
                menus.push( dropdownMenu );

                // detach it and append it to the body
                $( 'body' ).append( dropdownMenu.detach() );

                // grab the new offset position
                let eOffset = target.offset();

                // make sure to place it where it would normally go (this could be improved)
                dropdownMenu.css( {
                    'display': 'block',
                    'top': eOffset.top + target.outerHeight(),
                    'left': eOffset.left
                } );
            } );

            // and when you hide it, reattach the drop down, and hide it normally
            $( '.dataTables_scrollBody' ).on( 'hide.bs.dropdown', function( e ) {

                menus.forEach( function( element, index ) {
                    let parent = parents[index];
                    let dropdownMenu = element;

                    parent.append( dropdownMenu.detach() );
                    dropdownMenu.hide();

                    menus.splice( index, 1 );
                    parents.splice( index, 1 );
                } )
            } );

            new PerfectScrollbar(".header-notifications-list")
            
            $( '#_logout' ).click( function() {
                $.ajax( {
                    url: '{{ route( 'admin.logoutLog' ) }}',
                    type: 'POST',
                    data: { '_token': '{{ csrf_token() }}' },
                    success: function() {
                        document.getElementById( 'logout-form' ).submit();
                    }
                } );
            } );
            
            $( document ).on( 'focus', '.form-control', function() {
                if( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).text( '' );
                }
            } );

            $( document ).on( 'focus', '.form-select', function() {
                if( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).text( '' );
                }
            } );

            $( document ).on( 'hidden.bs.offcanvas', '.offcanvas-right', function() {
                $( '.offcanvas-body .form-control' ).removeClass( 'is-invalid' ).val( '' );
                $( '.invalid-feedback' ).text( '' );
                $( '.offcanvas-body .form-select' ).removeClass( 'is-invalid' ).val( '' );
            } );

            $( document ).on( 'hidden.bs.modal', '.modal', function() {
                $( '.modal .form-control' ).removeClass( 'is-invalid' ).val( '' ).nextAll( 'div.invalid-feedback' ).text( '' );
                $( '.modal .form-select' ).removeClass( 'is-invalid' ).val( '' ).nextAll( 'div.invalid-feedback' ).text( '' );
            } );
        } );

        $( '.notification-row' ).click( function() {

            let that = $( this );

            if ( that.data( 'url' ) == '' ) {
                return 0;
            }

            $.ajax( {
                url: '{{ route( 'admin.updateNotificationSeen' ) }}',
                type: 'POST',
                data: {
                    id: that.data( 'id' ),
                    '_token': '{{ csrf_token() }}'
                },
                success: function( request ) {
                    window.location.href = that.data( 'url' );
                },
            } );
        } );

        function resetInputValidation() {

            $( '.form-control' ).each( function( i, v ) {
                if ( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).html( '' );
                }
            } );

            $( '.form-select' ).each( function( i, v ) {
                if ( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).nextAll( 'div.invalid-feedback' ).html( '' );
                }
            } );
        }
        
        $(function() {
            "use strict";

            $(".nav-toggle-icon").on("click", function() {
                $(".wrapper").toggleClass("toggled")
            })

            $(".mobile-toggle-icon").on("click", function() {
                $(".wrapper").addClass("toggled")
            })

            // $(function() {
            //     for (var e = window.location, o = $(".metismenu li a").filter(function() {
            //             return this.href == e
            //         }).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
            // })


            $(".toggle-icon").click(function() {
                $(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function() {
                    $(".wrapper").addClass("sidebar-hovered")
                }, function() {
                    $(".wrapper").removeClass("sidebar-hovered")
                }))
            })



            $(function() {
                $("#menu").metisMenu()
            })


            $(".search-toggle-icon").on("click", function() {
                $(".top-header .navbar form").addClass("full-searchbar")
            })
            $(".search-close-icon").on("click", function() {
                $(".top-header .navbar form").removeClass("full-searchbar")
            })


            $(".chat-toggle-btn").on("click", function() {
                $(".chat-wrapper").toggleClass("chat-toggled")
            }), $(".chat-toggle-btn-mobile").on("click", function() {
                $(".chat-wrapper").removeClass("chat-toggled")
            }), $(".email-toggle-btn").on("click", function() {
                $(".email-wrapper").toggleClass("email-toggled")
            }), $(".email-toggle-btn-mobile").on("click", function() {
                $(".email-wrapper").removeClass("email-toggled")
            }), $(".compose-mail-btn").on("click", function() {
                $(".compose-mail-popup").show()
            }), $(".compose-mail-close").on("click", function() {
                $(".compose-mail-popup").hide()
            })


            $(document).ready(function() {
                $(window).on("scroll", function() {
                    $(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
                }), $(".back-to-top").on("click", function() {
                    return $("html, body").animate({
                        scrollTop: 0
                    }, 600), !1
                })
            })

            $(document).ajaxStart(function() { Pace.restart(); });
        });
    </script>