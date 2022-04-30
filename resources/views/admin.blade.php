@if(session()->has("error"))
    <div class="row">
        <div class="col">
            @include("alerts.error", ["message" => session()->get("error")])
        </div>
    </div>
@endif
@if(session()->has("success"))
    <div class="row">
        <div class="col">
            @include("alerts.success", ["message" => session()->get("success")])
        </div>
    </div>
@endif
<div class="row mb-2">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark text-white"><i class="far fa-users"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Active Customers</span>
                <span class="info-box-number">{{ $total_active_customers }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark text-white"><i class="far fa-user-times"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Off-Board Customers</span>
                <span class="info-box-number">{{ $total_off_board_customers }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark text-white"><i class="far fa-user-tie"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Drivers</span>
                <span class="info-box-number">{{ $total_drivers }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark text-white"><i class="fas fa-shopping-cart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Orders</span>
                <span class="info-box-number">{{ $total_orders }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title">Annual Bin Order Statistics - {{ $year }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <canvas id="visitors-chart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@if($show_myob_div)
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="https://secure.myob.com/oauth2/account/authorize?client_id={{ env("MYOB_API_KEY") }}&redirect_uri={{ env("APP_URL")."/dashboard" }}&response_type=code&scope=CompanyFile"
                               class="btn btn-dark">
                                Connect To MYOB Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@section("page-scripts")
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        $(function () {
            const ticksStyle = {
                fontColor: '#495057',
                fontStyle: 'bold'
            }

            const mode = 'index'
            const intersect = true

            const $visitorsChart = $('#visitors-chart')

            $.ajax({url: "{{ route("stats.orders") }}", method: "GET"})
                .done(function (res) {
                    new Chart($visitorsChart, {
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: [
                                {
                                    type: 'line',
                                    data: res.data,
                                    backgroundColor: 'transparent',
                                    borderColor: '#000',
                                    pointBorderColor: '#000',
                                    pointBackgroundColor: '#000',
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                                mode: mode,
                                intersect: intersect
                            },
                            hover: {
                                mode: mode,
                                intersect: intersect
                            },
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        display: true,
                                    },
                                    ticks: $.extend({
                                        beginAtZero: true,
                                        suggestedMax: res.threshold,
                                    }, ticksStyle)
                                }],
                                xAxes: [{
                                    display: true,
                                    gridLines: {
                                        display: true
                                    },
                                    ticks: ticksStyle
                                }]
                            }
                        }
                    })
                })
                .fail(function (xhr) {
                    alert(xhr.statusText);
                });
        });
    </script>
@endsection
