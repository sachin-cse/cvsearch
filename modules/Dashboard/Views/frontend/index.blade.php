@extends('layouts.user')

@section('content')
<div class="upper-title-box">
    <h3>{{ __("Howdy, :name", ['name' => Auth::user()->nameOrEmail]) }}!!</h3>
    <div class="text">{{ __("Ready to jump back in?") }}</div>
</div>
<div class="row">
    @if(!empty($top_cards))
        @foreach($top_cards as $card)
            <div class="col-xl-{{ $card['size_md'] }} col-lg-6 col-md-6 col-sm-12">
                <div class="ui-item {{ $card['color'] }}">
                    <div class="left">
                        <i class="{{$card['icon2']}}"></i>
                    </div>
                    <div class="right">
                        <h4>{{$card['amount']}}</h4>
                        <p>{{$card['desc']}}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="row">

    <div class="col-lg-7">
        <!-- Graph widget -->
        <div class="graph-widget ls-widget">
            <div class="tabs-box">
                <div class="widget-title">
                    <h4>{{ (is_admin()) ? __('Order views') : __('Your Profile Views') }}</h4>
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>

                <div class="widget-content">
                    <canvas id="earning_chart"></canvas>
                    <script>
                        var views_chart_data = {!! json_encode($views_chart_data) !!};
                    </script>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- Notification Widget -->
        <div class="notification-widget ls-widget">
            <div class="widget-title">
                <h4>{{ __("Notifications") }}</h4>
            </div>
            <div class="widget-content">
                <ul class="notification-list">
                    @php $rows = $notifications @endphp
                    @include('Core::frontend.notification.notification-loop-item')
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    <script src="{{url('libs/chart_js/Chart.min.js')}}"></script>
    <script src="{{url('libs/daterange/moment.min.js')}}"></script>
    <script src="{{url('libs/daterange/daterangepicker.min.js?_ver='.config('app.version'))}}"></script>
    <script>
        var ctx = document.getElementById('earning_chart').getContext('2d');

        window.myMixedChart = new Chart(ctx, {
            type: 'line',
            data: views_chart_data,
            options: {
                layout: {
                    padding: 10,
                },

                legend: {
                    display: false
                },
                title: {
                    display: false
                },

                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: false
                        },
                        gridLines: {
                            borderDash: [6, 10],
                            color: "#d8d8d8",
                            lineWidth: 1,
                        },
                        ticks: {
                            beginAtZero: true,
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: false
                        },
                        gridLines: {
                            display: false
                        },
                    }],
                },

                tooltips: {
                    backgroundColor: '#333',
                    titleFontSize: 13,
                    titleFontColor: '#fff',
                    bodyFontColor: '#fff',
                    bodyFontSize: 13,
                    displayColors: false,
                    xPadding: 10,
                    yPadding: 10,
                    intersect: false
                }
            }
        });

        var start = moment().startOf('week');
        var end = moment();
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            "alwaysShowCalendars": true,
            "opens": "left",
            "showDropdowns": true,
            ranges: {
                '{{__("Today")}}': [moment(), moment()],
                '{{__("Yesterday")}}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{__("Last 7 Days")}}': [moment().subtract(6, 'days'), moment()],
                '{{__("Last 30 Days")}}': [moment().subtract(29, 'days'), moment()],
                '{{__("This Month")}}': [moment().startOf('month'), moment().endOf('month')],
                '{{__("Last Month")}}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                '{{__("This Year")}}': [moment().startOf('year'), moment().endOf('year')],
                '{{__('This Week')}}': [moment().startOf('week'), end]
            }
        }, cb).on('apply.daterangepicker', function (ev, picker) {
            // Reload Earning JS
            $.ajax({
                url: '{{ route('admin.reloadChart') }}',
                data: {
                    chart: 'views',
                    from: picker.startDate.format('YYYY-MM-DD'),
                    to: picker.endDate.format('YYYY-MM-DD'),
                },
                dataType: 'json',
                type: 'post',
                success: function (res) {
                    if (res.status) {
                        window.myMixedChart.data = res.data;
                        window.myMixedChart.update();
                    }
                }
            })
        });
        cb(start, end);
    </script>
@endsection
