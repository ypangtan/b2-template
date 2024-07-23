            <!--start sidebar -->
            <aside class="sidebar-wrapper" data-simplebar="true">
                <div class="sidebar-header">
                    <div>
                        <!-- admin/img/icons/default.png -->
                        <img src="{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" class="logo-icon" alt="logo icon" />
                    </div>
                    
                    <div class="toggle-icon ms-auto"><i class="bi bi-list"></i></div>
                </div>
                <!--navigation-->
                <ul class="metismenu" id="menu">
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\DashboardController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.dashboard.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="sliders"></i></div>
                            <div class="menu-title">{{ __( 'template.dashboard' ) }}</div>
                        </a>
                    </li>
                    @can( 'view administrators' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AdministratorController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.administrator.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="user"></i></div>
                            <div class="menu-title">{{ __( 'template.administrators' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view roles' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\RoleController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.role.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="shield"></i></div>
                            <div class="menu-title">{{ __( 'template.roles' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view modules' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\ModuleController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.module.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="list-checks"></i></div>
                            <div class="menu-title">{{ __( 'template.modules' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view audits' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AuditController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.audit.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="file-search"></i></div>
                            <div class="menu-title">{{ __( 'template.audit_logs' ) }}</div>
                        </a>
                    </li>
                    @endcan

                    <li class="menu-label">{{ __( 'template.operations' ) }}</li>
                    @can( 'view users' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\UserController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.user.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="users"></i></div>
                            <div class="menu-title">{{ __( 'template.users' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view wallets' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\WalletController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.wallet.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="wallet"></i></div>
                            <div class="menu-title">{{ __( 'template.wallets' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view wallet_transactions' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\WalletTransactionController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.wallet_transaction.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="arrow-left-right"></i></div>
                            <div class="menu-title">{{ __( 'template.wallet_transactions' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view announcements' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AnnouncementController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.announcement.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="megaphone"></i></div>
                            <div class="menu-title">{{ __( 'template.announcements' ) }}</div>
                        </a>
                    </li>
                    @endcan

                    @can( 'view settings' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\SettingController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.setting.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" data-lucide="cog"></i></div>
                            <div class="menu-title">{{ __( 'template.settings' ) }}</div>
                        </a>
                    </li>
                    @endcan
                </ul>
                <!--end navigation-->
            </aside>
            <!--end sidebar -->