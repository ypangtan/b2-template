<?php echo view( 'admin/header' ); ?>

    <div class="wrapper">
        <?php echo view( 'admin/nav', array( 'controller' => $controller, 'action' => @$action ) ); ?>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
                <form class="d-none d-sm-inline-block" onsubmit="return false;">
                    <div class="input-group input-group-navbar">
                        <input type="text" class="form-control" id="search_order" placeholder="{{ __( 'template.search_order' ) }}" aria-label="Search Order" autocomplete="off">
                        <button class="btn" id="search_order_button" type="button">
                            <i class="align-middle" data-feather="search"></i>
                        </button>
                    </div>
                </form>
                <script>
                    document.addEventListener( 'DOMContentLoaded', function() {
                        $( '#search_order' ).on( 'keyup', function( e ) {
                            if( e.keyCode === 13 ) {
                                window.location.href="{{ Helper::baseAdminUrl() }}/orders/view/" + $( this ).val();
                            }
                        } );

                        $( '#search_order_button' ).click( function() {
                            window.location.href="{{ Helper::baseAdminUrl() }}/orders/view/" + $( '#search_order' ).val();
                        } );
                    } );
                </script>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="true">
                                <span class="text-dark">
                                    {{ Config::get('languages')[App::getLocale()] }}
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
@foreach( Config::get( 'languages' ) as $lang => $language )
@if( $lang != App::getLocale() )
                                <a class="dropdown-item" href="{{ Helper::baseAdminUrl() }}/lang/{{ $lang }}">
                                    <span class="align-middle">{{ $language }}</span>
                                </a>
@endif
@endforeach
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <span class="text-dark"><?=auth()->user()->username?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('admin.logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById( 'logout-form' ).submit();">{{ __( 'template.logout' ) }}</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">
                <div class="container-fluid p-0">
                    <?php echo view( $content, [ 'data' => @$data ] ); ?>
                </div>
            </main>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
            </form>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a class="text-muted" href="{{ Helper::baseAdminUrl() }}/dashboard" target="_blank"><strong>{{ Helper::websiteName() }}</strong></a> ©
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php echo view( 'admin/footer' ); ?>