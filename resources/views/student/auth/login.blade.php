<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Login — Inline CRM</title>
    <link rel="icon" type="image/x-icon" href="{{ url('crm/assets/img/favicon/favicon.ico') }}" />
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
                        <h1 class="text-2xl font-bold text-slate-900">Student Portal</h1>
                        <p class="mt-1 text-sm text-slate-500">Sign in to manage your application</p>
                    </div>

                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('student.authenticate') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" placeholder="your@email.com" autofocus required />
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
