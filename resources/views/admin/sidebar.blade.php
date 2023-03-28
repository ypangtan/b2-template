            <!--start sidebar -->
            <aside class="sidebar-wrapper" data-simplebar="true">
                <div class="sidebar-header">
                    <div>
                        <img src="{{ asset( 'admin/img/icons/default.png' ) }}" class="logo-icon" alt="logo icon" />
                    </div>
                    
                    <div class="toggle-icon ms-auto"><i class="bi bi-list"></i></div>
                </div>
                <!--navigation-->
                <ul class="metismenu" id="menu">
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\DashboardController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.dashboard.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="sliders"></i></div>
                            <div class="menu-title">{{ __( 'template.dashboard' ) }}</div>
                        </a>
                    </li>
                    @if ( auth()->user()->role == 1 )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AdministratorController' ? 'mm-active' : '' }}">
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="user"></i></div>
                            <div class="menu-title">{{ __( 'template.administrators' ) }}</div>
                        </a>
                        <ul>
                            <li class="{{ $controller == 'App\Http\Controllers\Admin\AdministratorController' && $action == 'index' ? 'mm-active' : '' }}">
                                <a class="metismenu-child" href="{{ route( 'admin.administrator.index' ) }}"><i class="bi bi-circle"></i>{{ __( 'template.list' ) }}</a>
                            </li>
                            <li class="{{ $action == 'role' ? 'mm-active' : '' }}">
                                <a class="metismenu-child" href="{{ route( 'admin.administrator.role' ) }}"><i class="bi bi-circle"></i>{{ __( 'template.roles' ) }}</a>
                            </li>
                            <li class="{{ $action == 'module' ? 'mm-active' : '' }}">
                                <a class="metismenu-child" href="{{ route( 'admin.administrator.module' ) }}"><i class="bi bi-circle"></i>{{ __( 'template.modules' ) }}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AuditController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.audit.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="file-search"></i></div>
                            <div class="menu-title">{{ __( 'template.audit_logs' ) }}</div>
                        </a>
                    </li>
                    <li class="menu-label">{{ __( 'template.operations' ) }}</li>
                    @endif
                </ul>
                <!--end navigation-->
            </aside>
            <!--end sidebar -->