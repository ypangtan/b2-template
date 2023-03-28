    <script src="{{ asset( 'admin/js/bootstrap.bundle.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery-3.5.1.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/jquery.dataTables.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/dataTables.bootstrap5.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/simplebar/js/simplebar.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/metismenu/js/metisMenu.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/pace.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lucide.min.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/flatpickr-4.6.9.js' ) . Helper::assetVersion() }}"></script>
    <script src="{{ asset( 'admin/js/lightgallery.min.js' ) . Helper::assetVersion() }}"></script>

    <script>
        lucide.createIcons();

        Number.prototype.toFixedDown = function(digits) {
		var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
			m = this.toString().match(re);
		return m ? parseFloat(m[1]).toFixed(digits) : this.valueOf().toFixed( 2 ).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        document.addEventListener( 'DOMContentLoaded', function() {

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
                    $( this ).removeClass( 'is-invalid' ).next().text( '' );
                }
            } );

            $( document ).on( 'focus', '.form-select', function() {
                if( $( this ).hasClass( 'is-invalid' ) ) {
                    $( this ).removeClass( 'is-invalid' ).next().text( '' );
                }
            } );

            $( document ).on( 'hidden.bs.offcanvas', '.offcanvas-right', function() {
                $( '.offcanvas-body .form-control' ).removeClass( 'is-invalid' ).val( '' );
                $( '.invalid-feedback' ).text( '' );
                $( '.offcanvas-body .form-select' ).removeClass( 'is-invalid' ).val( '' );
            } );

            $( '.form-control-plaintext' ).focus( function() {
                $( this ).blur();
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
                    $( this ).removeClass( 'is-invalid' ).next().html( '' );
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