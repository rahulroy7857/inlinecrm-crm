<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inline CRM') }} | @yield('title')</title>
    @include('account.layouts.header')
    @yield('style')
</head>
<body>
    <div id="sidebar-overlay" class="hidden" aria-hidden="true"></div>

    <div id="app-layout">
        @include('account.layouts.menu')

        <div class="layout-page">
            @include('account.layouts.navbar')

            <div class="content-wrapper">
                <main class="flex-1 pb-16">
                    @yield('content')
                </main>
                @include('account.layouts.footer')
            </div>
        </div>
    </div>

    <div class="modal fade" id="financialYearModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('account.change-financial-year') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Financial Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="academic_year" class="form-label">Select Financial Year</label>
                    @if(academic_years()->count() > 0)
                        <select id="academic_year" name="academic_year" class="form-control" required>
                            @foreach(academic_years() as $year)
                                <option value="{{ $year->id }}" {{ session('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="alert alert-warning mb-0">No financial years available.</div>
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

    @if(session()->has('academic_year_check'))
    <div id="yearToast" class="bs-toast toast bg-info" role="alert" data-bs-delay="3000"
         style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
        <div class="toast-header">
            <i class="bx bx-check-circle me-2 text-sky-600"></i>
            <div class="me-auto fw-semibold">Financial Year</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">Financial year updated successfully.</div>
    </div>
    @endif

    @include('account.layouts.scripts')
    @include('counselor.partials.toast-stack')
    @include('admin.partials.delete-confirm-modal')
    @yield('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session()->has('academic_year_check'))
            new bootstrap.Toast(document.getElementById('yearToast')).show();
            {{ session()->forget('academic_year_check') }}
        @endif
    });
    </script>
</body>
</html>
