<div class="card h-100">
    <div class="card-header border-bottom">
        <h5 class="mb-0">Application Status</h5>
    </div>
    <div class="card-body mt-3">
        <div class="row mb-4">
            <div class="col-md-3">
                <small class="text-muted">Lead ID</small>
                <p class="fw-semibold mb-0">{{ $student->lead_ref }}</p>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Course</small>
                <p class="fw-semibold mb-0">{{ $student->course?->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Status</small>
                <p class="mb-0"><span class="badge bg-primary">{{ $student->applicationStatusLabel() }}</span></p>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Submitted On</small>
                <p class="fw-semibold mb-0">{{ $student->submitted_at?->format('d M Y, h:i A') ?? 'Not yet' }}</p>
            </div>
        </div>

        <div class="progress mb-4" style="height: 8px;">
            @php
                $steps = ['registered' => 25, 'profile_completed' => 50, 'submitted' => 100, 'under_review' => 100, 'approved' => 100];
                $progress = $steps[$student->application_status] ?? 10;
            @endphp
            <div class="progress-bar" style="width: {{ $progress }}%"></div>
        </div>

        <ul class="list-unstyled mb-4">
            <li class="mb-2">
                <i class="bx {{ $student->isProfileComplete() ? 'bx-check-circle text-success' : 'bx-circle text-muted' }}"></i>
                Profile completed
            </li>
            <li class="mb-2">
                <i class="bx {{ $student->hasPaid() ? 'bx-check-circle text-success' : 'bx-circle text-muted' }}"></i>
                Application fee paid
            </li>
            <li>
                <i class="bx {{ $student->application_status === 'submitted' ? 'bx-check-circle text-success' : 'bx-circle text-muted' }}"></i>
                Application submitted
            </li>
        </ul>

        @if($student->application_status !== 'submitted')
            @if($student->isProfileComplete() && $student->hasPaid())
                <form method="POST" action="{{ route('student.application.submit') }}" onsubmit="return confirm('Submit your application? PDF copies will be emailed to you and your counselor.');">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-send me-1"></i> Submit Application
                    </button>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    Complete your profile and payment before submitting.
                    @if(!$student->isProfileComplete())
                        <a href="{{ route('student.profile.complete') }}">Complete profile</a>
                    @endif
                    @if(!$student->hasPaid())
                        | <a href="{{ route('student.payment.index') }}">Make payment</a>
                    @endif
                </div>
            @endif
        @else
            <div class="alert alert-success mb-0">
                Your application has been submitted. You and your counselor will receive PDF copies by email.
            </div>
        @endif
    </div>
</div>
