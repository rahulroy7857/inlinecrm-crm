<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inline CRM') }} | @yield('title')</title>
    @include('student.layouts.header')
    @yield('style')
</head>
<body>
    <div id="sidebar-overlay" class="hidden" aria-hidden="true"></div>

    <div id="app-layout">
        @include('student.layouts.menu')

        <div class="layout-page">
            @include('student.layouts.navbar')

            <div class="content-wrapper">
                <main class="flex-1 pb-16">
                    @yield('content')
                </main>
                @include('student.layouts.footer')
            </div>
        </div>
    </div>

    @include('student.layouts.scripts')
    @include('counselor.partials.toast-stack')
    @yield('scripts')
</body>
</html>
