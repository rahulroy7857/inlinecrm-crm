<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Login — Inline CRM</title>
    <meta name="description" content="Sign in to Inline CRM accounts portal." />
    <link rel="icon" type="image/x-icon" href="{{ url('crm/assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
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
</body>
</html>
