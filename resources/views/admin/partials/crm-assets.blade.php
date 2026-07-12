@if (file_exists(public_path('hot')))
    @include('partials.golos-text-font')
    @vite(['resources/css/crm.css', 'resources/js/crm.js'])
@elseif (file_exists(public_path('crm/dist/crm.css')))
    @include('partials.golos-text-font')
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ url('crm/dist/crm.css') }}?v={{ filemtime(public_path('crm/dist/crm.css')) }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @if (file_exists(public_path('crm/dist/crm.js')))
        <script src="{{ url('crm/dist/crm.js') }}?v={{ filemtime(public_path('crm/dist/crm.js')) }}" defer></script>
    @endif
@else
    {{-- Fallback when compiled CSS is missing — run: npm run css:crm --}}
    @include('partials.golos-text-font')
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ url('crm/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ url('crm/assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ url('crm/assets/css/demo.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
@endif
