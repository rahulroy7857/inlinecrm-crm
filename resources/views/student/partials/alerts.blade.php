@if(session('success'))
    <div class="alert alert-success auto-hide-alert">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger auto-hide-alert">{{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info auto-hide-alert">{{ session('info') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
