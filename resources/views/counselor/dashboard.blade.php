@extends('counselor.layouts.app')
@section('title', 'Dashboard')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  
  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <h1 style="color: #fff !important;">Welcome Back rahul test {{ auth()->user()->name }}! 👋</h1>
    <p>Here's your lead performance overview for {{ session('academic_year_name') }}</p>
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
        <a href="{{ route('counselor.leads.status', ['status' => 'new']) }}" class="stretched-link"></a>
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
        <a href="{{ route('counselor.leads.status', ['status' => 'warm']) }}" class="stretched-link"></a>
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
        <a href="{{ route('counselor.leads.status', ['status' => 'hot']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-bar-chart-alt-2"></i>
        </div>
        <div class="card-title">Applications</div>
        <h3>{{ number_format($leadsCount['application']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['application']}}% from last month
        </div>
        <a href="{{ route('counselor.leads.status', ['status' => 'application']) }}" class="stretched-link"></a>
      </div>
    </div>

    <div class="stats-card">
      <div class="card-body">
        <div class="icon-bg">
          <i class="bx bx-bar-chart-alt-2"></i>
        </div>
        <div class="card-title">Admissions</div>
        <h3>{{ number_format($leadsCount['admission']) }}</h3>
        <div class="trend">
          <i class="bx bx-trending-up"></i> +{{$leadsPercentageDiff['admission']}}% from last month
        </div>
        <a href="{{ route('counselor.leads.status', ['status' => 'admission']) }}" class="stretched-link"></a>
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
    <div class="col-lg-6 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-funnel me-2"></i>Lead Conversion Funnel</h5>
        </div>
        <div class="card-body">
          <div id="leadFunnelChart"></div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 mb-4">
      <div class="chart-card">
        <div class="card-header">
          <h5><i class="bx bx-calendar-check me-2"></i>{{date('Y')}} Follow-up Performance</h5>
        </div>
        <div class="card-body">
          <div id="todaysFollowupsChart"></div>
        </div>
      </div>
    </div>
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