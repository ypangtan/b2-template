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
                            <div class="parent-icon"><i class="align-middle feather" icon-name="sliders"></i></div>
                            <div class="menu-title">{{ __( 'template.dashboard' ) }}</div>
                        </a>
                    </li>
                    @if ( auth()->user()->role == 1 )
                    @can( 'view administrators' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AdministratorController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.administrator.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="user"></i></div>
                            <div class="menu-title">{{ __( 'template.administrators' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view roles' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\RoleController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.role.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="shield"></i></div>
                            <div class="menu-title">{{ __( 'template.roles' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    @can( 'view modules' )
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\ModuleController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.module.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="list-checks"></i></div>
                            <div class="menu-title">{{ __( 'template.modules' ) }}</div>
                        </a>
                    </li>
                    @endcan
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\AuditController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.audit.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="file-search"></i></div>
                            <div class="menu-title">{{ __( 'template.audit_logs' ) }}</div>
                        </a>
                    </li>
                    @endif

                    <li class="menu-label">{{ __( 'template.operations' ) }}</li>
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\CategoryController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.category.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="boxes"></i></div>
                            <div class="menu-title">{{ __( 'template.categories' ) }}</div>
                        </a>
                    </li>
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\ProductController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.product.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="box"></i></div>
                            <div class="menu-title">{{ __( 'template.products' ) }}</div>
                        </a>
                    </li>
                    <li class="{{ $controller == 'App\Http\Controllers\Admin\OrderController' ? 'mm-active' : '' }}">
                        <a href="{{ route( 'admin.module_parent.order.index' ) }}">
                            <div class="parent-icon"><i class="align-middle feather" icon-name="building-2"></i></div>
                            <div class="menu-title">{{ __( 'template.orders' ) }}</div>
                        </a>
                    </li>
                </ul>
                <!--end navigation-->
            </aside>
            <!--end sidebar -->