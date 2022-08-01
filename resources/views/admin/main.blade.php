<?php echo view( 'admin/header', [ 'header' => @$header ] );?>

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
                                <i class="align-middle feather" icon-name="settings"></i>
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

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="#28a745" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="#dc3545" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>
    
    <?php echo view( 'admin/footer' ); ?>