<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inline CRM') }} | @yield('title')</title>
    @include('counselor.layouts.header')
    @yield('style')
</head>
<body>
    <div id="sidebar-overlay" class="hidden" aria-hidden="true"></div>

    <div id="app-layout">
        @include('counselor.layouts.menu')

        <div class="layout-page">
            @include('counselor.layouts.navbar')

            <div class="content-wrapper">
                <main class="flex-1 pb-16">
                    @yield('content')
                </main>
                @include('counselor.layouts.footer')
            </div>
        </div>
    </div>

    <!-- Modal: Change Academic Year -->
    <div class="modal fade" id="academicYearModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('counselor.change-academic-year') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="academic_year" class="form-label">Select Academic Year</label>
                    @if(academic_years()->count() > 0)
                        <select id="academic_year" name="academic_year" class="form-control" required>
                            @foreach(academic_years() as $year)
                                <option value="{{ $year['id'] }}" {{ session('academic_year_id') == $year['id'] ? 'selected' : '' }}>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    @if(academic_years()->count() > 0)
                        <button type="submit" class="btn btn-primary">Change</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @include('counselor.layouts.scripts')
    @include('counselor.partials.toast-stack')
    @include('admin.partials.delete-confirm-modal')
    @include('admin.partials.pick-confirm-modal')

    <div class="modal fade" id="breakLoginLockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bx bx-lock-alt me-1"></i> Admin Permission Required
                    </h5>
                </div>
                <div class="modal-body">
                    <p id="breakLoginLockMessage" class="mb-2">
                        Your break time has exceeded the allowed limit. Please contact your admin to grant login permission.
                    </p>
                    <p class="mb-0 text-muted small">You can login again once an admin approves your access.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')

    <script>
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

    $(document).ready(function() {
        updateCounts();
        setInterval(updateCounts, 30000);
        document.addEventListener('leadAdded', updateCounts);
        document.addEventListener('leadUpdated', updateCounts);
        document.addEventListener('leadDeleted', updateCounts);
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.querySelectorAll('.auto-hide-alert').forEach(function(alert) {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 2500);
    });

    (function () {
        var loginUrl = @json(route('counselor.login'));
        var originalFetch = window.fetch;

        window.fetch = function () {
            return originalFetch.apply(this, arguments).then(function (response) {
                if (response.status !== 403) {
                    return response;
                }

                return response.clone().json().then(function (data) {
                    if (data && data.break_login_locked) {
                        try {
                            sessionStorage.setItem('break_login_lock_message', data.message || '');
                        } catch (e) {}
                        window.location.href = data.redirect || loginUrl;
                    }
                    return response;
                }).catch(function () {
                    return response;
                });
            });
        };
    })();
    </script>
</body>
</html>
