                    <h1 class="h2 mb-3"><strong>{{ __( 'dashboard.analytics' ) }}</strong>{{ __( 'dashboard.dashboard' ) }}</h1>

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
                                                    <h1 class="mt-1 mb-3 card-value" id="nu">
                                                        <div class="spinner-border" style="border-width: 0.05em" role="status">
                                                            <span class="visually-hidden">{{ __( 'template.loading' ) }}</span>
                                                        </div>
                                                    </h1>
                                                    <a href="{{ Helper::baseBranchUrl() }}/customers" class="stretched-link"></a>
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
                                                <h1 class="mt-1 mb-3 card-value" id="to">
                                                    <div class="spinner-border" style="border-width: 0.05em" role="status">
                                                        <span class="visually-hidden">{{ __( 'template.loading' ) }}</span>
                                                    </div>
                                                </h1>
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
                                                <h1 class="mt-1 mb-3 card-value" id="en">
                                                    <div class="spinner-border" style="border-width: 0.05em" role="status">
                                                        <span class="visually-hidden">{{ __( 'template.loading' ) }}</span>
                                                    </div>
                                                </h1>
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
                                                <h1 class="mt-1 mb-3 card-value" id="tmo">
                                                    <div class="spinner-border" style="border-width: 0.05em" role="status">
                                                        <span class="visually-hidden">{{ __( 'template.loading' ) }}</span>
                                                    </div>
                                                </h1>
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
                                url: '{{ Helper::baseBranchUrl() }}/dashboard/monthly_sales',
                                type: 'POST',
                                data: { _token: '{{ csrf_token() }}' },
                                success: function( response ) {

                                    chart_data.labels = response.months;
                                    chart_data.datasets[0].data = response.earnings;

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