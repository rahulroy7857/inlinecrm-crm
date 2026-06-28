@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  
  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <h1>Welcome back, {{ auth()->guard('admin')->user()->name }}!</h1>
    <p>Here's your comprehensive overview for {{ session('academic_year_name') }}</p>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <!-- <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-user-plus"></i>
        </div>
        <div class="card-title">New Leads</div>
        <h3>{{ number_format($leadsCount['new']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['new']}}% from last month
        </div>
        <a href="{{ route('admin.leads.status', ['status' => 'new']) }}" class="stretched-link"></a>
      </div>
    </div> -->

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-bar-chart-alt-2"></i>
        </div>
        <div class="card-title">Warm Leads</div>
        <h3>{{ number_format($leadsCount['warm']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['warm']}}% from last month
        </div>
        <a href="{{ route('admin.leads.status', ['status' => 'warm']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-bar-chart-alt-2"></i>
        </div>
        <div class="card-title">Hot Leads</div>
        <h3>{{ number_format($leadsCount['hot']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['hot']}}% from last month
        </div>
        <a href="{{ route('admin.leads.status', ['status' => 'hot']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-file"></i>
        </div>
        <div class="card-title">Applications</div>
        <h3>{{ number_format($leadsCount['application']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['application']}}% from last month
        </div>
        <a href="{{ route('admin.leads.status', ['status' => 'application']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-building"></i>
        </div>
        <div class="card-title">Admissions</div>
        <h3>{{ number_format($leadsCount['admission']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['admission']}}% from last month
        </div>
        <a href="{{ route('admin.leads.status', ['status' => 'admission']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-bar-chart-alt-2"></i>
        </div>
        <div class="card-title">Total Leads</div>
        <h3>{{ number_format($leadsCount['total']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['total']}}% from last month
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="row">
    <div class="col-12 col-lg-8 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-bar-chart-alt-2 me-2"></i>Leads Overview {{date('Y')}}</h5>
        </div>
        <div class="card-body">
          <div id="monthlyLeadsChart"></div>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-lg-4 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-funnel me-2"></i>Lead Funnel {{session('academic_year_name')}}</h5>
        </div>
        <div class="card-body">
          <div id="leadFunnelChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-lg-6 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-calendar-check me-2"></i>{{date('Y')}} Follow-ups</h5>
        </div>
        <div class="card-body">
          <div id="todaysFollowupsChart"></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-dollar-sign me-2"></i>{{date('Y')}} Revenue</h5>
        </div>
        <div class="card-body">
          <div id="receivedPaymentsChart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12 col-md-12">

      <!-- <div class="row mb-4">
        <div class="col-md-12 col-xxl-4 mb-6">
          <div class="card h-100">
            <div class="d-flex align-items-end row">
              <div class="col-7">
                <div class="card-body">
                  <h5 class="card-title mb-1 text-nowrap">{{ $topCounselor->name ?? 'No Counselor' }} 🎉</h5>
                  <p class="card-subtitle text-nowrap mb-3">Top performer of the month</p>

                  <h5 class="card-title text-primary mb-0">{{ $topCounselor->admissions_count ?? 0 }}</h5>
                  <p class="mb-3">Admissions🚀</p>

                </div>
              </div>
              <div class="col-5">
                <div class="card-body pb-0 text-end">
                  <img src="/crm/assets/img/prize-light.png" width="91" height="144" class="rounded-start" alt="View Sales">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 mb-6">
        <div class="card h-100">
                                  <div class="card-body">
                                    <div class="d-flex justify-content-between flex-sm-row flex-column gap-3" style="position: relative;">
                                      <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                        <div class="card-title">
                                          <h5 class="text-nowrap mb-2">Admission</h5>
                                          <span class="badge bg-label-warning rounded-pill">Year 2025</span>
                                        </div>
                                        <div class="mt-sm-auto">
                                          <small class="text-success text-nowrap fw-semibold"><i class="bx bx-chevron-up"></i> 68.2%</small>
                                          <h3 class="mb-0">120</h3>
                                        </div>
                                      </div>
                                      <div id="profileReportChart" style="min-height: 80px;"><div id="apexchartstjc5f1f7" class="apexcharts-canvas apexchartstjc5f1f7 apexcharts-theme-light" style="width: 200px; height: 80px;"><svg id="SvgjsSvg1685" width="200" height="80" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1687" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)"><defs id="SvgjsDefs1686"><clipPath id="gridRectMasktjc5f1f7"><rect id="SvgjsRect1692" width="201" height="85" x="-4.5" y="-2.5" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMasktjc5f1f7"></clipPath><clipPath id="nonForecastMasktjc5f1f7"></clipPath><clipPath id="gridRectMarkerMasktjc5f1f7"><rect id="SvgjsRect1693" width="196" height="84" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><filter id="SvgjsFilter1699" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feFlood id="SvgjsFeFlood1700" flood-color="#ffab00" flood-opacity="0.15" result="SvgjsFeFlood1700Out" in="SourceGraphic"></feFlood><feComposite id="SvgjsFeComposite1701" in="SvgjsFeFlood1700Out" in2="SourceAlpha" operator="in" result="SvgjsFeComposite1701Out"></feComposite><feOffset id="SvgjsFeOffset1702" dx="5" dy="10" result="SvgjsFeOffset1702Out" in="SvgjsFeComposite1701Out"></feOffset><feGaussianBlur id="SvgjsFeGaussianBlur1703" stdDeviation="3 " result="SvgjsFeGaussianBlur1703Out" in="SvgjsFeOffset1702Out"></feGaussianBlur><feMerge id="SvgjsFeMerge1704" result="SvgjsFeMerge1704Out" in="SourceGraphic"><feMergeNode id="SvgjsFeMergeNode1705" in="SvgjsFeGaussianBlur1703Out"></feMergeNode><feMergeNode id="SvgjsFeMergeNode1706" in="[object Arguments]"></feMergeNode></feMerge><feBlend id="SvgjsFeBlend1707" in="SourceGraphic" in2="SvgjsFeMerge1704Out" mode="normal" result="SvgjsFeBlend1707Out"></feBlend></filter></defs><line id="SvgjsLine1691" x1="0" y1="0" x2="0" y2="80" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="80" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG1708" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG1709" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g></g><g id="SvgjsG1717" class="apexcharts-grid"><g id="SvgjsG1718" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine1720" x1="0" y1="0" x2="192" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1721" x1="0" y1="20" x2="192" y2="20" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1722" x1="0" y1="40" x2="192" y2="40" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1723" x1="0" y1="60" x2="192" y2="60" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine1724" x1="0" y1="80" x2="192" y2="80" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG1719" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine1726" x1="0" y1="80" x2="192" y2="80" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine1725" x1="0" y1="1" x2="0" y2="80" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG1694" class="apexcharts-line-series apexcharts-plot-series"><g id="SvgjsG1695" class="apexcharts-series" seriesName="seriesx1" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath1698" d="M 0 76C 13.44 76 24.96 12 38.4 12C 51.839999999999996 12 63.36 62 76.8 62C 90.24 62 101.75999999999999 22 115.19999999999999 22C 128.64 22 140.16 38 153.6 38C 167.04 38 178.56 6 192 6" fill="none" fill-opacity="1" stroke="rgba(255,171,0,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMasktjc5f1f7)" filter="url(#SvgjsFilter1699)" pathTo="M 0 76C 13.44 76 24.96 12 38.4 12C 51.839999999999996 12 63.36 62 76.8 62C 90.24 62 101.75999999999999 22 115.19999999999999 22C 128.64 22 140.16 38 153.6 38C 167.04 38 178.56 6 192 6" pathFrom="M -1 120L -1 120L 38.4 120L 76.8 120L 115.19999999999999 120L 153.6 120L 192 120"></path><g id="SvgjsG1696" class="apexcharts-series-markers-wrap" data:realIndex="0"><g class="apexcharts-series-markers"><circle id="SvgjsCircle1732" r="0" cx="0" cy="0" class="apexcharts-marker w8ia4v1mkj no-pointer-events" stroke="#ffffff" fill="#ffab00" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g></g><g id="SvgjsG1697" class="apexcharts-datalabels" data:realIndex="0"></g></g><line id="SvgjsLine1727" x1="0" y1="0" x2="192" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1728" x1="0" y1="0" x2="192" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG1729" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG1730" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG1731" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect1690" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect><g id="SvgjsG1716" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG1688" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 40px;"></div><div class="apexcharts-tooltip apexcharts-theme-light"><div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"></div><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(255, 171, 0);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-light"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                    <div class="resize-triggers"><div class="expand-trigger"><div style="width: 339px; height: 118px;"></div></div><div class="contract-trigger"></div></div></div>
                                  </div>
                                </div>
        </div>


          






        <div class="col-xxl-4 mb-6">
          <div class="card">
            <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column align-items-center gap-1">
                      <h2 class="mb-2">{{ $conversionRate }}%</h2>
                      <span>Convertion Rate</span>
                  </div>
                  <div id="orderStatisticsChart"></div>
                </div>
            </div>
          </div>
        </div>





        


      </div> -->




          <div class="row">
                          <!-- Order Statistics -->
                          <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                            <div class="chart-card h-100">
                              <div class="card-header">
                                <h5><i class="bx bx-user-check me-2"></i>Recent Admission</h5>
                              </div>
                              <div class="card-body">
                                <ul class="p-0 m-0">

                                  @foreach($recentAdmissions as $admission)
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            @include('admin.partials.lead-avatar', ['photo' => $admission->photo, 'name' => $admission->name])
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">{{ $admission->name }}</h6>
                                                <small class="text-muted">{{ $admission->college->name ?? 'N/A' }}, {{ $admission->course->name ?? 'N/A' }}</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">{{ $admission->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                               
                                </ul>
                              </div>
                            </div>
                          </div>
                          <!--/ Order Statistics -->
          
                          <!-- Expense Overview -->
                          <div class="col-md-6 col-lg-4 order-1 mb-4">
                            <div class="chart-card h-100">
                              <div class="card-header">
                                <h5><i class="bx bx-pie-chart me-2"></i>Leads Status {{session('academic_year_name')}}</h5>
                              </div>
                                <div class="card-body">
                                <div id="leadsStatusPieChart" class="mb-3" style="width: 100%; height: 400px;"></div>
                                
                                </div>
                            </div>
                          </div>
                          <!--/ Expense Overview -->
          
                          <!-- Transactions -->
                          <div class="col-md-6 col-lg-4 order-2 mb-4">
                            <div class="chart-card h-100">
                              <div class="card-header">
                                <h5><i class="bx bx-transfer me-2"></i>Recent Transactions</h5>
                              </div>
                              <div class="card-body">
                                <ul class="p-0 mt-3">
                                  @foreach($recentTransactions as $transaction)
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            @include('admin.partials.lead-avatar', ['photo' => $transaction->lead->photo ?? null, 'name' => $transaction->lead->name ?? 'User'])
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">{{ $transaction->payment_mode }}</small>
                                                <h6 class="mb-0">{{ $transaction->lead->name }}</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">{{ number_format($transaction->amount, 2) }}</h6>
                                                <span class="text-muted">INR</span>
                                            </div>
                                        </div>
                                    </li>
                                  @endforeach
                                </ul>
                              </div>
                            </div>
                          </div>
                          <!--/ Transactions -->
                        </div>
</div>

<div
  id="infoToast"
  class="bs-toast toast bg-info"
  role="alert"
  aria-live="assertive"
  aria-atomic="true"
  data-bs-autohide="true">
  <div class="toast-header">
    <i class="bx bx-bell me-2"></i>
    <div class="me-auto fw-semibold">Followup Alert</div>
    <small>4 mins ago</small>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    New leads have been added to your call log.
  </div>
</div>


@endsection
@section('scripts')  
<script>
  // document.addEventListener("DOMContentLoaded", function () {
  //   setTimeout(function () {
  //     var infoToast = document.getElementById("infoToast");
  //     if (infoToast) {
  //       var toast = new bootstrap.Toast(infoToast);
  //       infoToast.style.position = "fixed";
  //       infoToast.style.bottom = "20px";
  //       infoToast.style.right = "20px";
  //       infoToast.style.zIndex = "1050";
  //       infoToast.style.display = "block";
  //       toast.show();
  //     }
  //   }, 100); // Delay execution to avoid blocking other components
  // });
</script>
<script>
                                  document.addEventListener("DOMContentLoaded", function () {
                                  var leadsStatusPieChartElement = document.querySelector("#leadsStatusPieChart");
                                  if (leadsStatusPieChartElement) {
                                    var options = {
                                    series: @json(array_values($leadStatusDistribution)),// Example data: Adjust as needed
                                    chart: {
                                      type: 'pie',
                                      width: '100%',
                                      height: 400
                                    },
                                    labels: @json(array_keys($leadStatusDistribution)),
                                    colors: @json($statusColors),
                                    legend: {
                                      position: 'bottom'
                                    },
                                    tooltip: {
                                      y: {
                                      formatter: function (val) {
                                        return val + "%";
                                      }
                                      }
                                    }
                                    };

                                    var chart = new ApexCharts(leadsStatusPieChartElement, options);
                                    chart.render();
                                  } else {
                                    console.error("Element #leadsStatusPieChart not found.");
                                  }
                                  });
                                </script>
<script>
              document.addEventListener("DOMContentLoaded", function () {
              var receivedPaymentsChartElement = document.querySelector("#receivedPaymentsChart");
              if (receivedPaymentsChartElement) {
              var options = {
              series: [{
                name: "Revenue",
                data: @json(array_values($monthlyRevenueData))
              }],
              chart: {
                type: 'line',
                height: 320,
                toolbar: {
                show: true,
                tools: {
                  download: false, // Only export icon enabled
                  selection: false,
                  zoom: false,
                  zoomin: false,
                  zoomout: false,
                  pan: false,
                  reset: false
                }
                }
              },
              stroke: {
                curve: 'smooth'
              },
              xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
              },
              colors: ['#7367F0'],
              tooltip: {
                y: {
                formatter: function (val) {
                return "₹" + val;
                }
                }
              }
              };

              var chart = new ApexCharts(receivedPaymentsChartElement, options);
              chart.render();
              } else {
              console.error("Element #receivedPaymentsChart not found.");
              }
              });
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                var todaysFollowupsChartElement = document.querySelector("#todaysFollowupsChart");
                if (todaysFollowupsChartElement) {
                var options = {
                series: [
                {
                    name: "Positive Follow-ups",
                    data: @json($followupsData->pluck('positive_count'))
                },
                {
                    name: "Negative Follow-ups",
                    data: @json($followupsData->pluck('negative_count'))
                }
                ],
                chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                  show: true,
                  tools: {
                  download: false, // Disable export button
                  selection: false,
                  zoom: false,
                  zoomin: false,
                  zoomout: false,
                  pan: false,
                  reset: false
                  }
                }
                },
                plotOptions: {
                bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
                }
                },
                xaxis: {
                    categories: @json($months)
                },
                colors: ['#28C76F', '#EA5455'],
                tooltip: {
                y: {
                formatter: function (val) {
                return val + " follow-ups";
                }
                }
                }
                };

                var chart = new ApexCharts(todaysFollowupsChartElement, options);
                chart.render();
                } else {
                console.error("Element #todaysFollowupsChart not found.");
                }
                });
            </script>
<script>
              document.addEventListener("DOMContentLoaded", function () {
              var monthlyLeadsChartElement = document.querySelector("#monthlyLeadsChart");
              if (monthlyLeadsChartElement) {
              var options = {
              series: [
                { name: "Total Leads", data: @json(array_values($monthlyLeadsData)) },
                
              ],
              chart: {
                type: 'line',
                height: 350,
                toolbar: {
                show: true,
                tools: {
                  download: false, // Only export icon enabled
                  selection: false,
                  zoom: false,
                  zoomin: false,
                  zoomout: false,
                  pan: false,
                  reset: false
                }
                }
              },
              stroke: {
                curve: 'smooth'
              },
              xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
              },
              colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#00CFE8', '#FFC107', '#FF4560'],
              tooltip: {
                y: {
                formatter: function (val) {
                return val + " leads";
                }
                }
              }
              };

              var chart = new ApexCharts(monthlyLeadsChartElement, options);
              chart.render();
              } else {
              console.error("Element #monthlyLeadsChart not found.");
              }
              });
            </script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
      funnelData = @json($funnelData);
      var funnelPercentages = @json($funnelPercentages);
      var leadFunnelChartElement = document.querySelector("#leadFunnelChart");
      if (leadFunnelChartElement) {
        var options = {
          series: [{
            data: [
                        funnelData.total,
                        funnelData.positive,
                        funnelData.application,
                        funnelData.admission
                  ]
          }],
          chart: {
            type: 'bar',
            height: 320,
            toolbar: {
              show: true,
              tools: {
                download: false, // Hide export button
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false
              }
            }
          },
          plotOptions: {
            bar: {
              horizontal: true,
              barHeight: '70%',
              distributed: true
            }
          },
          dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                const stage = ['total', 'positive', 'application', 'admission'][opts.dataPointIndex];
                return val + " (" + funnelPercentages[stage] + "%)";
            }
          },
          xaxis: {
            categories: ['Total Leads', 'Positive', 'Application Filled', 'Admission']
          },
          colors: ['#6f42c1', '#ffc107', '#0d6efd', '#198754'],
          tooltip: {
            y: {
              formatter: function (val) {
                return val + " leads";
              }
            }
          }
        };

        var chart = new ApexCharts(leadFunnelChartElement, options);
        chart.render();
      } else {
        console.error("Element #leadFunnelChart not found.");
      }
    });
</script>
@endsection