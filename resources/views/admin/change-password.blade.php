@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('style')   

@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Change Password</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="row d-flex justify-content-center">
                    <div class="col-12 col-md-6 col-lg-6">
                        <form action="{{ route('admin.change-password.update') }}" method="POST" 
                              id="changePasswordForm"
                              style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    <button class="btn btn-outline-secondary toggle-password" 
                                            type="button" 
                                            data-target="current_password">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           required>
                                    <button class="btn btn-outline-secondary toggle-password" 
                                            type="button" 
                                            data-target="new_password">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    Password must be at least 8 characters and contain uppercase, lowercase, numbers and symbols
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           required>
                                    <button class="btn btn-outline-secondary toggle-password" 
                                            type="button" 
                                            data-target="new_password_confirmation">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Password</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const target = $(this).data('target');
        const input = $(`#${target}`);
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bx-hide').addClass('bx-show');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bx-show').addClass('bx-hide');
        }
    });

    // Form validation
    $('#changePasswordForm').submit(function(e) {
        const newPass = $('#new_password').val();
        const confirmPass = $('#new_password_confirmation').val();

        if (newPass !== confirmPass) {
            e.preventDefault();
            alert('The password confirmation does not match.');
        }
    });
});
</script>
@endsection