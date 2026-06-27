@if(session('success'))
<div class="bs-toast toast bg-success auto-hide-toast" 
     role="alert" 
     aria-live="assertive" 
     aria-atomic="true" 
     style="position: fixed; top: 20px; right: 20px; z-index: 1080;">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">Success</div>
        <small>Now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="bs-toast toast bg-danger auto-hide-toast"
     role="alert"
     aria-live="assertive"
     aria-atomic="true" 
     style="position: fixed; top: 20px; right: 20px; z-index: 1080;">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">Error</div>
        <small>Now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ session('error') }}
    </div>
</div>
@endif

@if ($errors->any())
<div class="bs-toast toast fade show bg-danger auto-hide-toast"
     role="alert"
     aria-live="assertive"
     aria-atomic="true"
     style="position: absolute; top: 20px; right: 20px; z-index: 1080;"
     id="validationToast">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">Error</div>
        <small>Now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif