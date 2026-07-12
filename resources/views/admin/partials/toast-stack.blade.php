<div id="toast-stack" class="toast-stack" aria-live="polite" aria-atomic="true"></div>

@if(session('success'))
<div class="toast-stack-item" data-toast-type="success" data-toast-message="{{ session('success') }}"></div>
@endif
@if(session('error'))
<div class="toast-stack-item" data-toast-type="error" data-toast-message="{{ session('error') }}"></div>
@endif
@if ($errors->any())
<div class="toast-stack-item" data-toast-type="error" data-toast-message="{{ $errors->first() }}"></div>
@endif
