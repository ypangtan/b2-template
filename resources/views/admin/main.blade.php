<?php echo view( 'admin/header', [ 'header' => @$header ] );?>

    <body>
        <!--start wrapper-->
        <div class="wrapper">
            <!--start top header-->
            <header class="top-header">
                <nav class="navbar navbar-expand gap-3">
                    <div class="mobile-toggle-icon fs-3">
                        <i class="bi bi-list"></i>
                    </div>
                    @if ( 1 == 2 )
                    <form class="searchbar">
                        <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
                        <input class="form-control" type="text" placeholder="Type here to search" />
                        <div class="position-absolute top-50 translate-middle-y search-close-icon"><i class="bi bi-x-lg"></i></div>
                    </form>
                    @endif
                    <?php
                    $notification = Helper::administratorNotifications();
                    $totalUnread = $notification['total_unread'];
                    $notifications = $notification['notifications'];
                    ?>
                    <div class="top-navbar-right ms-auto">
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret notification-dropdown" href="#" data-bs-toggle="dropdown">
                                    <div class="notifications">
                                        @if ( $totalUnread > 0 )
                                        <span class="notify-badge">{{ $totalUnread }}</span>
                                        @endif
                                        <i class="bi bi-bell-fill"></i>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-0">
                                    <div class="p-2 border-bottom m-2">
                                        <h5 class="h5 mb-2">Notifications</h5>
                                    </div>
                                    @if ( count( $notifications ) == 0 )
                                    <div class="text-center mb-2 header-notifications-list">No notifications found.</div>
                                    @else
                                    <div class="header-notifications-list px-2">
                                        @foreach( $notifications as $n )
                                        <?php
                                        $meta = json_decode( $n->meta_data );
                                        ?>
                                        <a class="dropdown-item notification-row {{ $n->is_read ? 'notification-read' : '' }}" href="javacsript:;" data-id="{{ $n->id }}" data-url="{{ isset( $meta->url ) ? Helper::baseAdminUrl() . $meta->url : '' }}">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="notification-box bg-light-primary text-primary"><i class="bi bi-person-badge"></i></div>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    @if ( $n->type == 1 )
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="mb-0 dropdown-msg-user">{{ __( $n->system_title ) }}</h6>
                                                        <span class="msg-time float-end" style="margin-left: 5px;">{{ Helper::getDisplayTimeUnit( $n->created_at ) }}</span>
                                                    </div>
                                                    <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">{{ __( $n->system_content ) }}</small>
                                                    @else
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="mb-0 dropdown-msg-user">{{ $n->title }}</h6>
                                                        <span class="msg-time float-end" style="margin-left: 5px;">{{ Helper::getDisplayTimeUnit( $n->created_at ) }}</span>
                                                    </div>
                                                    <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">{{ $n->content }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                    <div class="p-2">
                                        <div><hr class="dropdown-divider" /></div>
                                        <a class="dropdown-item" href="#">
                                            <div class="text-center">View All Notifications</div>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                    <?php


                    $role = [ 
                        '', 
                        __( 'role.super_admin' ), 
                        __( 'role.admin' ), 
                    ];
                    ?>
                    <div class="dropdown dropdown-user-setting">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <div class="user-setting d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?background=3461ff&color=fff&name={{ auth()->user()->name }}" alt="" class="user-img" style="" />
                                <div class="d-none d-sm-block">
                                    <p class="user-name mb-0">{{ auth()->user()->name }}</p>
                                    <small class="mb-0 dropdown-user-designation">{{ @$role[auth()->user()->role] }}</small>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route( 'admin.profile.index' ) }}">
                                    <div class="d-flex align-items-center">
                                        <div class=""><i class="bi bi-person-lines-fill"></i></div>
                                        <div class="ms-3"><span>{{ __( 'template.profile' ) }}</span></div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" id="_logout" href="#">
                                    <div class="d-flex align-items-center">
                                        <div class=""><i class="bi bi-lock-fill"></i></div>
                                        <div class="ms-3"><span>{{ __( 'template.logout' ) }}</span></div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--end top header-->
            <?php echo view( 'admin/sidebar', [ 'header' => @$header, 'controller' => $controller, 'action' => @$action ] );?>

            <main class="page-content">

                @if( @$breadcrumbs['enabled'] )
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">{{ $breadcrumbs['main_title'] }}</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 d-flex align-items-center">
                            <i class="align-middle feather" data-lucide="sliders" style="color: #3461ff; width: 16px; height: 16px;"></i>
                            <i class="align-middle feather" data-lucide="chevron-right" style="width: 32px; height: 32px; stroke-width: 1.3;"></i>
                            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumbs['title'] }}</li>
                        </ol>
                        </nav>
                    </div>
                </div>
                @endif

                <h6 class="mobile-listing-header mb-0 text-uppercase">{{ @$breadcrumbs['mobile_title'] }}</h6>
                <hr class="mobile-listing-header">

                <?php echo view( $content, [ 'data' => @$data ] ); ?>

                <x-modal-confirmation />
                <x-modal-success />
                <x-modal-danger />
            </main>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
            </form>

            <!--start overlay-->
            <div class="overlay nav-toggle-icon"></div>
            <!--end overlay-->
        </div>

        <?php echo view( 'admin/footer' ); ?>
    </body>
</html>