        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ Helper::baseAdminUrl() }}/dashboard">
                    <span class="align-middle">{{ Helper::websiteName() }}</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ $controller == 'DashboardController' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/dashboard">
                            <i class="align-middle feather" icon-name="sliders"></i> <span class="align-middle">{{ __( 'template.dashboard' ) }}</span>
                        </a>
                    </li>

                    @can( 'view admins' )
                    <li class="sidebar-item {{ $controller == 'AuditController' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ Helper::baseAdminUrl() }}/audits">
                            <i class="align-middle feather" icon-name="file-text"></i> <span class="align-middle">{{ __( 'template.audit_logs' ) }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ $controller == 'AdministratorController' ? 'active' : '' }}">
                        <a data-bs-target="#administrator_child" data-bs-toggle="collapse" class="sidebar-link {{ $controller == 'AdministratorController' ? '' : 'collapsed' }}">
                            <i class="align-middle feather" icon-name="user"></i> <span class="align-middle">{{ __( 'template.administrator' ) }}</span>
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
                            <i class="align-middle feather" icon-name="users"></i> <span class="align-middle">{{ __( 'template.customers' ) }}</span>
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
                </ul>
            </div>
        </nav>