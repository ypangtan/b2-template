        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ Helper::baseBranchUrl() }}/dashboard">
                    <span class="align-middle">Wash La! Branch</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-item <?=$controller == 'DashboardController' ? 'active' : '';?>">
                        <a class="sidebar-link" href="{{ Helper::baseBranchUrl() }}/dashboard">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">{{ __( 'template.dashboard' ) }}</span>
                        </a>
                    </li>

                    @canany( [ 'view branches', 'view branch_accounts', 'view branch_orders' ] )
                    <li class="sidebar-item <?=$controller == 'BranchController' ? 'active' : '';?>">
                        <a data-bs-target="#branch_child" data-bs-toggle="collapse" class="sidebar-link <?=$controller == 'BranchController' ? '' : 'collapsed';?>">
                            <i class="align-middle" data-feather="globe"></i> <span class="align-middle">{{ __( 'template.branch' ) }}</span>
                        </a>
                        <ul id="branch_child" class="sidebar-dropdown list-unstyled collapse <?=$controller == 'BranchController' ? 'show' : 'collapsed';?>" data-bs-parent="#sidebar">
                            @can( 'view branches' )
                            <li class="sidebar-item <?=$controller == 'BranchController' ? $action == 'index' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseBranchUrl() }}/branches">{{ __( 'template.list' ) }}</a>
                            </li>
                            @endcan
                            @can( 'view branch_accounts' )
                            <li class="sidebar-item <?=$controller == 'BranchController' ? $action == 'accounts' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseBranchUrl() }}/branches/staffs">{{ __( 'template.branch_staff' ) }}</a>
                            </li>
                            @endcan
                            @can( 'view branch_orders' )
                            <li class="sidebar-item <?=$controller == 'BranchController' ? $action == 'orders' ? 'active' : '' : '';?>">
                                <a class="sidebar-link" href="{{ Helper::baseBranchUrl() }}/branches/orders">{{ __( 'template.branch_order' ) }}</a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany
                </ul>
            </div>
        </nav>