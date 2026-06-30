<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Registration — Inline CRM</title>
    <link rel="icon" type="image/x-icon" href="{{ url('crm/assets/img/favicon/favicon.ico') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    @include('admin.partials.crm-assets')
</head>
<body class="auth-bg">
    <div class="authentication-wrapper">
        <div class="authentication-inner" style="max-width: 520px;">
            <div class="card">
                <div class="card-body">
                    <div class="mb-6 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg">IC</div>
                        <h1 class="text-2xl font-bold text-slate-900">Student Registration</h1>
                        @if($leadRef)
                            <p class="mt-1 text-sm text-slate-500">Lead ID: <strong>{{ $leadRef }}</strong></p>
                        @endif
                    </div>

                    @if($error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @elseif($lead)
                        @include('student.partials.alerts')

                        <form method="POST" action="{{ route('student.registration.lead.store', $leadRef) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $lead->name) }}" required />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $lead->personal_email) }}" required />
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    value="{{ old('mobile', $lead->mobile) }}" required />
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-control" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ old('country', $lead->country) == $country ? 'selected' : '' }}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <select class="form-control" id="state" name="state" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            @if($state !== 'Any')
                                            <option value="{{ $state }}" {{ old('state', $lead->state) == $state ? 'selected' : '' }}>{{ $state }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $lead->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required />
                            </div>

                            <button class="btn btn-primary d-grid w-100" type="submit">Create Account</button>
                        </form>

                        <p class="mt-4 mb-0 text-center text-sm text-slate-500">
                            Already registered? <a href="{{ route('student.login') }}">Sign in</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
