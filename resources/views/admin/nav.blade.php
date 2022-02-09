        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ Helper::baseAdminUrl() }}/dashboard">
                    <span class="align-middle">{{ Helper::websiteName() }}</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ $controller == 'DashboardController' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/dashboard">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">{{ __( 'template.dashboard' ) }}</span>
                        </a>
                    </li>

                    @can( 'view admins' )
                    <li class="sidebar-item {{ $controller == 'AdministratorController' ? 'active' : '' }}">
                        <a data-bs-target="#administrator_child" data-bs-toggle="collapse" class="sidebar-link {{ $controller == 'AdministratorController' ? '' : 'collapsed' }}">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">{{ __( 'template.administrator' ) }}</span>
                        </a>
                        <ul id="administrator_child" class="sidebar-dropdown list-unstyled collapse {{ $controller == 'AdministratorController' ? 'show' : 'collapsed' }}" data-bs-parent="#sidebar">
                            <li class="sidebar-item {{ $controller == 'AdministratorController' ? $action == 'index' ? 'active' : '' : '' }}">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/administrators">{{ __( 'template.list' ) }}</a>
                            </li>
                            <li class="sidebar-item {{ $controller == 'AdministratorController' ? $action == 'role' ? 'active' : '' : '' }}">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/administrators/roles">{{ __( 'template.roles' ) }}</a>
                            </li>
                            <li class="sidebar-item {{ $controller == 'AdministratorController' ? $action == 'module' ? 'active' : '' : '' }}">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/administrators/modules">{{ __( 'template.modules' ) }}</a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can( 'view customers' )
                    <li class="sidebar-item {{ $controller == 'CustomerController' ? 'active' : '' }}">
                        <a data-bs-target="#customer_child" data-bs-toggle="collapse" class="sidebar-link {{ $controller == 'CustomerController' ? '' : 'collapsed' }}">
                            <i class="align-middle" data-feather="users"></i> <span class="align-middle">{{ __( 'template.customers' ) }}</span>
                        </a>
                        <ul id="customer_child" class="sidebar-dropdown list-unstyled collapse {{ $controller == 'CustomerController' ? 'show' : 'collapsed' }}" data-bs-parent="#sidebar">
                            <li class="sidebar-item {{ $controller == 'CustomerController' ? $action == 'index' ? 'active' : '' : '' }}">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/customers">{{ __( 'template.list' ) }}</a>
                            </li>
                            <li class="sidebar-item {{ $controller == 'CustomerController' ? $action == 'address' ? 'active' : '' : '' }}">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/customers/addresses">{{ __( 'template.addresses' ) }}</a>
                            </li>
                        </ul>
                    </li>
                    @endcan
                    
                    @if( request()->user()->role == 1 )
                        <li class="sidebar-header">
                            {{ __( 'template.operations' ) }}
                        </li>
                    @else
                        @if( count( request()->user()->getAllPermissions() ) != 0 )
                        <li class="sidebar-header">
                            {{ __( 'template.operations' ) }}
                        </li>
                        @endif
                    @endif

                    @can( 'view suppliers' )
                    <li class="sidebar-item <?=$controller == 'SupplierController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/suppliers">
                            <i class="align-middle" data-feather="globe"></i> <span class="align-middle">{{ __( 'template.suppliers' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view orders' )
                    <li class="sidebar-item <?=$controller == 'OrderController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/orders">
                            <i class="align-middle" data-feather="shopping-bag"></i> <span class="align-middle">{{ __( 'template.orders' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view categories' )
                    <li class="sidebar-item <?=$controller == 'CategoryController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/categories">
                            <i class="align-middle" data-feather="grid"></i> <span class="align-middle">{{ __( 'template.categories' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view products' )
                    <li class="sidebar-item <?=$controller == 'ProductController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/products">
                            <i class="align-middle" data-feather="package"></i> <span class="align-middle">{{ __( 'template.products' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view promotions' )
                    <li class="sidebar-item <?=$controller == 'PromotionController' ? 'active' : '';?>">
                        <a data-bs-target="#promotion_child" data-bs-toggle="collapse" class="sidebar-link <?=$controller == 'PromotionController' ? '' : 'collapsed';?>">
                            <i class="align-middle" data-feather="award"></i> <span class="align-middle">{{ __( 'template.promotions' ) }}</span>
                        </a>
                        <ul id="promotion_child" class="sidebar-dropdown list-unstyled collapse <?=$controller == 'PromotionController' ? 'show' : 'collapsed';?>" data-bs-parent="#sidebar">
                            <li class="sidebar-item <?=$controller == 'PromotionController' ? $action == 'voucher' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/vouchers">{{ __( 'template.vouchers' ) }}</a>
                            </li>
                            <li class="sidebar-item <?=$controller == 'PromotionController' ? $action == 'voucherUsage' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/vouchers/usage">{{ __( 'template.voucher_usages' ) }}</a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can( 'view wallets' )
                    <li class="sidebar-item <?=$controller == 'WalletController' ? 'active' : '';?>">
                        <a data-bs-target="#payment_child" data-bs-toggle="collapse" class="sidebar-link <?=$controller == 'WalletController' ? '' : 'collapsed';?>">
                            <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">{{ __( 'template.wallets' ) }}</span>
                        </a>
                        <ul id="payment_child" class="sidebar-dropdown list-unstyled collapse <?=$controller == 'WalletController' ? 'show' : 'collapsed';?>" data-bs-parent="#sidebar">
                            <li class="sidebar-item <?=$controller == 'WalletController' ? $action == 'index' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/wallets">{{ __( 'template.wallets' ) }}</a>
                            </li>
                            <li class="sidebar-item <?=$controller == 'WalletController' ? $action == 'transaction' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/wallets/transactions">{{ __( 'template.transactions' ) }}</a>
                            </li>
                            <li class="sidebar-item <?=$controller == 'WalletController' ? $action == 'topup' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/wallets/topups">{{ __( 'template.topups' ) }}</a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can( 'view requests' )
                    <li class="sidebar-item <?=$controller == 'RequestController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/requests">
                            <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">{{ __( 'template.requests' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view settings' )
                    <li class="sidebar-item <?=$controller == 'SettingController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/settings">
                            <i class="align-middle" data-feather="settings"></i> <span class="align-middle">{{ __( 'template.settings' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view reports' )
                    <li class="sidebar-header">
                        {{ __( 'template.reports' ) }}
                    </li>
                    <li class="sidebar-item <?=$controller == 'ReportController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/reports/summary_report">
                            <i class="align-middle" data-feather="star"></i> <span class="align-middle">{{ __( 'template.summary_reports' ) }}</span>
                        </a>
                    </li>
                    @endcan

                    @can( 'view notifications' )
                    <li class="sidebar-header">
                        {{ __( 'template.misc' ) }}
                    </li>
                    <li class="sidebar-item <?=$controller == 'NotificationController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/notifications">
                            <i class="align-middle" data-feather="bell"></i> <span class="align-middle">{{ __( 'template.notifications' ) }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </nav>