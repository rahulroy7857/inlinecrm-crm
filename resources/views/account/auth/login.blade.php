<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Login — Inline CRM</title>
    <meta name="description" content="Sign in to Inline CRM accounts portal." />
    <link rel="icon" type="image/x-icon" href="{{ url('crm/assets/img/favicon/favicon.ico') }}" />
    @include('partials.golos-text-font')
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    @include('admin.partials.crm-assets')
</head>
<body class="auth-bg">
    <div class="authentication-wrapper">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <div class="mb-8 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg">IC</div>
                        <h1 class="text-2xl font-bold text-slate-900">Accounts Portal</h1>
                        <p class="mt-1 text-sm text-slate-500">Sign in to manage finances</p>
                    </div>

                    <form id="formAuthentication" method="POST" action="{{ route('account.authenticate') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" placeholder="accountant@inlinecrm.com" autofocus required />
                            @error('email')
                                <span class="mt-1 block text-sm text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="Enter your password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePassword()">
                                    <i class="bx bx-hide" id="toggleIcon"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="mt-1 block text-sm text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                    </form>
                </div>
            </div>

            <p class="mt-6 text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} Inline Infotech
            </p>
        </div>
    </div>

    <div class="modal fade" id="breakLoginLockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger"><i class="bx bx-lock-alt me-1"></i> Admin Permission Required</h5>
                </div>
                <div class="modal-body">
                    <p id="breakLoginLockMessage" class="mb-2">Your break time has exceeded the allowed limit. Please contact your admin to grant login permission.</p>
                    <p class="mb-0 text-muted small">You can login again once an admin approves your access from the account users panel.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bx-hide', 'bx-show');
        } else {
            input.type = 'password';
            icon.classList.replace('bx-show', 'bx-hide');
        }
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('break_login_locked'))
            var message = @json(session('break_login_lock_message', 'Admin permission is required to login again.'));
            document.getElementById('breakLoginLockMessage').textContent = message;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('breakLoginLockModal')).show();
        @endif
        try {
            var stored = sessionStorage.getItem('break_login_lock_message');
            if (stored) {
                sessionStorage.removeItem('break_login_lock_message');
                document.getElementById('breakLoginLockMessage').textContent = stored;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('breakLoginLockModal')).show();
            }
        } catch (e) {}
    });
    </script>
</body>
</html>
