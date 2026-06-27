<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

        @include('counselor.layouts.header')
        @yield('style')
        <style>
            .card-header h5 {
                margin-bottom: 0;
            }
            .btn {
                padding: 0.3375rem 1rem;
            }
            .badge-status {
                font-size: 0.85rem;
                padding: 0.35em 0.65em;
            }

            .bg-purple {
                background-color: #696cff !important;
                color: #fff;
            }

            .bg-indigo {
                background-color: #6610f2 !important;
                color: #fff;
            }

            .bg-danger-subtle {
                background-color: #ff949d !important;
                color: #930006;
            }

            .select2-container--default .select2-selection--single {
                height: 38px;
                border: 1px solid #d9dee3;
                border-radius: 0.375rem;
                padding: 5px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 26px;
                padding-left: 8px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
                right: 5px;
            }

            .select2-search--dropdown {
                padding: 8px;
            }

            .select2-search--dropdown .select2-search__field {
                padding: 6px;
                border: 1px solid #d9dee3;
                border-radius: 0.375rem;
            }

            .select2-search--dropdown .select2-search__field:focus {
                outline: none;
                border-color: #696cff;
            }
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                /* padding: 0.75rem 1.5rem; */
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            }
            .modal-header {
                background-color: #76e3f6 !important;
            }
            .modal-header h5 {
                color: #fff !important;
            }
            .offcanvas-header {
                background-color: #76e3f6 !important;
            }
            .offcanvas-header h5 {
                color: #fff !important;
            }
        </style>
    </head>
    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">

                <!-- Menu -->
                @include('counselor.layouts.menu')

                <!-- Page Content -->
                <div class="layout-page">
                    <!-- Navbar -->
                    @include('counselor.layouts.navbar')
                    <!-- / Navbar -->
                    <div class="content-wrapper">
                    <!-- Content -->
                        @yield('content')
                        <!-- Footer -->
                        @include('counselor.layouts.footer')
                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- / Content -->
                </div>
            </div>
        </div>


<!-- Modal change academic year -->
<div class="modal fade" id="academicYearModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('counselor.change-academic-year') }}" method="POST">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Change Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="academic_year" class="form-label">Select Academic Year</label>
                        @if(academic_years()->count() > 0)
                            <select id="academic_year" name="academic_year" class="form-control" required>
                                @foreach(academic_years() as $year)
                                    <option value="{{ $year['id'] }}" 
                                        {{ session('academic_year_id') == $year['id'] ? 'selected' : '' }}>
                                        {{ $year['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning mb-0">
                                No academic years available. Please add some first.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                @if(academic_years()->count() > 0)
                    <button type="submit" class="btn btn-primary">Change</button>
                @endif
            </div>
        </form>
    </div>
</div>

@if(session()->has('academic_year_check'))
<!-- Toast Notification -->
<div id="academicYearToast" 
     class="bs-toast toast bg-info"
     role="alert"
     aria-live="assertive"
     aria-atomic="true"
     data-bs-delay="3000"
     style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
    <div class="toast-header">
        <i class="bx bx-check-circle me-2"></i>
        <div class="me-auto fw-semibold">Academic Year</div>
        <small>Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        Academic year updated successfully.
    </div>
</div>
@endif
@include('counselor.layouts.scripts')
@yield('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session()->has('academic_year_check'))
        var toast = new bootstrap.Toast(document.getElementById('academicYearToast'));
        toast.show();
        {{ session()->forget('academic_year_check') }}
    @endif
});
function updateCounts() {
    $.ajax({
        url: '{{ route("counselor.get-counts") }}',
        method: 'GET',
        success: function(response) {
            $('#basket-leads-count').text(response.basket_leads);
            $('#new-leads-count').text(response.new_leads);
            $('#today-followups-count').text(response.today_followups);
            $('#tomorrow-followups-count').text(response.tomorrow_followups);
            $('#pending-followups-count').text(response.pending_followups);
            $('#bin-leads-count').text(response.bin);
        }
    });
}
// Update counts every 30 seconds
$(document).ready(function() {
    updateCounts(); // Initial update
    setInterval(updateCounts, 30000); // Update every 30 seconds
    
    // Update counts after certain actions
    document.addEventListener('leadAdded', updateCounts);
    document.addEventListener('leadUpdated', updateCounts);
    document.addEventListener('leadDeleted', updateCounts);
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.auto-hide-alert').forEach(function(alert) {
                // Bootstrap 5 alert close
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 2500); // 2.5 seconds
    });
</script>
</body>
</html>
