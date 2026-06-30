@extends('account.layouts.portal')
@section('title', 'Change Password')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')
    <div class="card">
        <div class="card-header border-bottom"><h5 class="mb-0">Change Password</h5></div>
        <div class="card-body mt-3">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="{{ account_route('change-password.update') }}" method="POST" id="changePasswordForm"
                          style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                            <small class="text-muted">Min 8 chars with uppercase, lowercase, numbers and symbols</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
