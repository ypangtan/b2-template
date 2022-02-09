                    <h1 class="h3 mb-3"><strong>{{ __( 'dashboard.analytics' ) }}</strong>{{ __( 'dashboard.dashboard' ) }}</h1>

                    <div class="row">
                        <div class="col-xl-6 col-xxl-5 d-flex">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col mt-0">
                                                            <h5 class="card-title">{{ __( 'dashboard.new_user' ) }}</h5>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="stat text-primary">
                                                                <i class="align-middle" data-feather="users"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h1 class="mt-1 mb-3">{{ $data['users_this'] }}</h1>
                                                    <div class="mb-0">
                                                        @if( $data['new_user_percent'] <= -1 )
                                                        <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_user_percent'] }}%</span>
                                                        @elseif( $data['new_user_percent'] == 0 )
                                                        <span class="text-secondary"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_user_percent'] }}%</span>
                                                        @else
                                                        <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_user_percent'] }}%</span>
                                                        @endif
                                                        <span class="text-muted">{{ __( 'dashboard.compare_last_month' ) }}</span>
                                                    </div>
                                                    <a href="{{ Helper::baseAdminUrl() }}/customers" class="stretched-link"></a>
                                                </div>
                                            </div>
                                        
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h5 class="card-title">{{ __( 'dashboard.today_order' ) }}</h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="stat text-primary">
                                                            <i class="align-middle" data-feather="shopping-cart"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h1 class="mt-1 mb-3">{{ $data['orders_today'] }}</h1>
                                                <div class="mb-0">
                                                    @if( $data['today_order_percent'] <= -1 )
                                                    <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['today_order_percent'] }}%</span>
                                                    @elseif( $data['today_order_percent'] == 0 )
                                                    <span class="text-secondary"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['today_order_percent'] }}%</span>
                                                    @else
                                                    <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['today_order_percent'] }}%</span>
                                                    @endif
                                                    <span class="text-muted">{{ __( 'dashboard.compare_yesterday' ) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h5 class="card-title">{{ __( 'dashboard.earnings' ) }}</h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="stat text-primary">
                                                            <i class="align-middle" data-feather="dollar-sign"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h1 class="mt-1 mb-3">MYR {{ Helper::numberFormat( $data['earnings_this'], 2 ) }}</h1>
                                                <div class="mb-0">
                                                    @if( $data['new_earning_percent'] <= -1 )
                                                    <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_earning_percent'] }}%</span>
                                                    @elseif( $data['new_earning_percent'] == 0 )
                                                    <span class="text-secondary"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_earning_percent'] }}%</span>
                                                    @else
                                                    <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['new_earning_percent'] }}%</span>
                                                    @endif
                                                    <span class="text-muted">{{ __( 'dashboard.compare_last_month' ) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h5 class="card-title">{{ __( 'dashboard.this_month_order' ) }}</h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="stat text-primary">
                                                            <i class="align-middle" data-feather="shopping-cart"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h1 class="mt-1 mb-3">{{ $data['orders_this_month'] }}</h1>
                                                <div class="mb-0">
                                                    @if( $data['this_month_order_percent'] <= -1 )
                                                    <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['this_month_order_percent'] }}%</span>
                                                    @elseif( $data['this_month_order_percent'] == 0 )
                                                    <span class="text-secondary"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['this_month_order_percent'] }}%</span>
                                                    @else
                                                    <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>{{ $data['this_month_order_percent'] }}%</span>
                                                    @endif
                                                    <span class="text-muted">{{ __( 'dashboard.compare_last_month' ) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-xxl-7">
                            <div class="card flex-fill w-100">
                                <div class="card-header">

                                    <h5 class="card-title mb-0">{{ __( 'dashboard.last_6_month_sales' ) }}</h5>
                                </div>
                                <div class="card-body py-3">
                                    <div class="chart chart-sm">
                                        <canvas id="chartjs-dashboard-line"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                    function renderOrderStatus( $index, $info, $total_order ) {

                        switch( $index ) {
                            case 'pending':
                                $properties['status'] = __( 'dashboard.pending' );
                                $properties['color'] = 'danger';
                                $properties['params'] = '?status=pending';
                                break;
                            case 'processing':
                                $properties['status'] = __( 'dashboard.processing' );
                                $properties['color'] = 'warning';
                                $properties['params'] = '?status=processing';
                                break;
                            case 'shipped':
                                $properties['status'] = __( 'dashboard.shipped' );
                                $properties['color'] = 'success';
                                $properties['params'] = '?status=shipped';
                                break;
                            case 'completed':
                                $properties['status'] = __( 'dashboard.completed' );
                                $properties['color'] = 'primary';
                                $properties['params'] = '?status=completed';
                                break;
                            case 'payment_failed':
                                $properties['status'] = __( 'dashboard.payment_failed' );
                                $properties['color'] = 'primary';
                                $properties['params'] = '?status=payment_failed';
                                break;
                        }

                        if( $info['TOTAL_ORDER'] == 0 ) {
                            $properties['percentage'] = 0;
                        } else {
                            $properties['percentage'] = round( ( $info['TOTAL_ORDER'] / $total_order * 100 ) * 2 ) * .5;
                        }

                    return $properties;
                    }

                    ?>
                    <div class="row">
                        <div class="col-12 col-lg-7 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __( 'dashboard.order_by_status' ) }}</h5>
                                </div>
                                <table class="table table-borderless my-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%">{{ __( 'dashboard.status' ) }}</th>
                                            <th class="d-none d-xxl-table-cell" style="width: 10%">{{ __( 'dashboard.total' ) }}</th>
                                            <th>{{ __( 'dashboard.percentage' ) }}</th>
                                            <th class="d-none d-xl-table-cell" style="width: 10%">{{ __( 'dashboard.action' ) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php foreach( $data['order_statuses'] as $os ): ?>
                                        <?php $props = renderOrderStatus( $os['STATUS'], $os, $data['total_order'] ); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 bg-<?=$props['color']?>" style="width: 18px; height: 18px; border-radius: 50%;">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <strong><?=$props['status']?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-xxl-table-cell">
                                                <strong><?=$os['TOTAL_ORDER'].'/'.$data['total_order']?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column w-100">
                                                    <span class="me-2 mb-1 text-muted"><?=$props['percentage']?>%</span>
                                                    <div class="progress progress-sm bg-<?=$props['color']?>-light w-100">
                                                        <div class="progress-bar bg-<?=$props['color']?>" role="progressbar" style="width: <?=$props['percentage']?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <a href="{{ Helper::baseAdminUrl() }}/orders<?=$props['params']?>" class="btn btn-light">{{ __( 'dashboard.view' ) }}</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __( 'dashboard.top_5_voucher_usage' ) }}</h5>
                                </div>
                                <table class="table table-borderless my-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __( 'dashboard.code' ) }}</th>
                                            <th class="text-end">{{ __( 'dashboard.total_used' ) }}</th>
                                            <th class="text-end">{{ __( 'dashboard.discounted_amount' ) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $data['voucher_usages'] as $vo )
                                        <tr>
                                            <td>{{ $vo->voucher->code }}</td>
                                            <td class="text-end">{{ $vo->TOTAL_USED }}</td>
                                            <td class="text-end">MYR {{ $vo->USAGE }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <a href="{{ Helper::baseAdminUrl() }}/vouchers/usage" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");
                            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
                            gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
                            gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
                            // Line chart

                            var chart_data = {
                                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                                datasets: [
                                    {
                                        label: "Sales (MYR)",
                                        fill: true,
                                        backgroundColor: gradient,
                                        borderColor: window.theme.primary,
                                        data: [
                                            500.4,
                                            611.2,
                                            816.90,
                                            900.95,
                                            1105,
                                            1280.13,
                                        ]
                                    }
                                ]
                            }

                            $.ajax( {
                                url: '{{ Helper::baseAdminUrl() }}/dashboard/monthly_sales',
                                type: 'POST',
                                data: { _token: '{{ csrf_token() }}' },
                                success: function( response ) {
                                    var res = JSON.parse( response );

                                    chart_data.labels = res.months;
                                    chart_data.datasets[0].data = res.earnings;

                                    new Chart(document.getElementById("chartjs-dashboard-line"), {
                                        type: "line",
                                        data: chart_data,
                                        options: {
                                            maintainAspectRatio: false,
                                            legend: {
                                                display: false
                                            },
                                            tooltips: {
                                                intersect: false
                                            },
                                            hover: {
                                                intersect: true
                                            },
                                            plugins: {
                                                filler: {
                                                    propagate: false
                                                }
                                            },
                                            scales: {
                                                xAxes: [{
                                                    reverse: true,
                                                    gridLines: {
                                                        color: "rgba(0,0,0,0.0)"
                                                    }
                                                }],
                                                yAxes: [{
                                                    ticks: {
                                                        stepSize: 1000
                                                    },
                                                    display: true,
                                                    borderDash: [3, 3],
                                                    gridLines: {
                                                        color: "rgba(0,0,0,0.0)"
                                                    }
                                                }]
                                            }
                                        }
                                    });
                                }
                            } );
                        });
                    </script>