<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inline CRM') }} | @yield('title')</title>
    @include('admin.layouts.header')
    @yield('style')
</head>
<body>
    <div id="sidebar-overlay" class="hidden" aria-hidden="true"></div>

    <div id="app-layout">
        @include('admin.layouts.menu')

        <div class="layout-page">
            @include('admin.layouts.navbar')

            <div class="content-wrapper">
                <main class="flex-1 pb-16">
                    @yield('content')
                </main>
                @include('admin.layouts.footer')
            </div>
        </div>
    </div>

    <!-- Modal: Change Academic Year -->
    <div class="modal fade" id="academicYearModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.change-academic-year') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="academic_year" class="form-label">Select Academic Year</label>
                    @if(isset($academicYears) && $academicYears->count() > 0)
                        <select id="academic_year" name="academic_year" class="form-control" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ session('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
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
                    @if(isset($academicYears) && $academicYears->count() > 0)
                        <button type="submit" class="btn btn-primary">Change</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @include('admin.layouts.scripts')
    @include('admin.partials.toast-stack')
    @include('admin.partials.delete-confirm-modal')
    @include('admin.partials.pick-confirm-modal')
    @yield('scripts')

    <script>
    function updateCounts() {
        $.ajax({
            url: '{{ route("admin.get-counts") }}',
            method: 'GET',
            success: function(response) {
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
    </script>
</body>
</html>
