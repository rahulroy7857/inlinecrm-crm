@php
    $isSubmitted = $student->isApplicationSubmitted();
    $progressPercent = $student->applicationProgressPercent();

    $steps = [
        [
            'key' => 'profile',
            'label' => 'Profile',
            'title' => 'Complete your profile',
            'description' => 'Add personal, family, and address details.',
            'icon' => 'bx-user',
            'done' => $student->isProfileComplete(),
            'action_url' => route('student.profile.complete'),
            'action_label' => 'Complete profile',
        ],
        [
            'key' => 'payment',
            'label' => 'Payment',
            'title' => 'Fee payment status',
            'description' => 'Track amounts paid and transaction IDs. Payments are collected by Accounts.',
            'icon' => 'bx-credit-card',
            'done' => $student->hasPaid(),
            'action_url' => route('student.payment.index'),
            'action_label' => 'View payments',
        ],
        [
            'key' => 'documents',
            'label' => 'Documents',
            'title' => 'Upload required documents',
            'description' => 'Upload photo, Aadhar, and marksheet (PDF, JPG, JPEG, or PNG).',
            'icon' => 'bx-cloud-upload',
            'done' => $student->hasRequiredDocuments(),
            'action_url' => '#student-documents',
            'action_label' => 'Upload files',
        ],
        [
            'key' => 'submit',
            'label' => 'Submit',
            'title' => 'Submit application',
            'description' => 'Review everything and submit your application.',
            'icon' => 'bx-send',
            'done' => $isSubmitted,
            'action_url' => null,
            'action_label' => 'Submit application',
        ],
    ];

    $activeIndex = collect($steps)->search(fn ($step) => ! $step['done']);
    if ($activeIndex === false) {
        $activeIndex = count($steps) - 1;
    }

    $documentsByType = $student->documents->keyBy('document_type');
    $canUploadDocuments = ! $isSubmitted;
    $canSubmit = $student->isProfileComplete() && $student->hasPaid() && $student->hasRequiredDocuments() && ! $isSubmitted;
@endphp

<div class="card h-100 student-journey-card portal-chart-card">
    <div class="card-header border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="mb-0">Application Journey</h5>
        <span class="badge rounded-pill student-journey-badge">{{ $progressPercent }}% Complete</span>
    </div>

    <div class="card-body">
        <div class="student-journey-meta row g-3 mb-4">
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Lead ID</small>
                <strong>{{ $student->lead_ref }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Course</small>
                <strong>{{ $student->course?->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Status</small>
                <span class="badge bg-primary">{{ $student->applicationStatusLabel() }}</span>
            </div>
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Submitted On</small>
                <strong>{{ $student->submitted_at?->format('d M Y, h:i A') ?? 'Not yet' }}</strong>
            </div>
        </div>

        <div class="student-journey-track mb-4">
            <div class="student-journey-track__bar">
                <div class="student-journey-track__fill" style="width: {{ $progressPercent }}%;"></div>
            </div>
            <div class="student-journey-steps">
                @foreach ($steps as $index => $step)
                    @php
                        $state = $step['done'] ? 'is-done' : ($index === $activeIndex ? 'is-active' : 'is-pending');
                    @endphp
                    <div class="student-journey-step {{ $state }}">
                        <div class="student-journey-step__circle">
                            @if ($step['done'])
                                <i class="bx bx-check"></i>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="student-journey-step__label">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="student-journey-panels">
            @foreach ($steps as $index => $step)
                @php
                    $state = $step['done'] ? 'is-done' : ($index === $activeIndex ? 'is-active' : 'is-pending');
                @endphp
                <div class="student-journey-panel {{ $state }}" @if($step['key'] === 'documents') id="student-documents" @endif>
                    <div class="student-journey-panel__icon">
                        <i class="bx {{ $step['icon'] }}"></i>
                    </div>
                    <div class="student-journey-panel__content">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                            <h6 class="mb-0">{{ $step['title'] }}</h6>
                            @if ($step['done'])
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Completed</span>
                            @elseif ($index === $activeIndex)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">In progress</span>
                            @else
                                <span class="badge bg-light text-muted border">Pending</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-2">{{ $step['description'] }}</p>

                        @if ($step['key'] === 'documents')
                            <div class="student-doc-grid mb-3">
                                @foreach (config('student.document_types', []) as $type => $label)
                                    @php $doc = $documentsByType->get($type); @endphp
                                    <div class="student-doc-item {{ $doc ? 'is-uploaded' : '' }}">
                                        <div class="student-doc-item__head">
                                            <i class="bx {{ $doc ? $doc->iconClass() : 'bx-file-blank' }}"></i>
                                            <div>
                                                <strong>{{ $label }}</strong>
                                                @if (in_array($type, config('student.required_documents', []), true))
                                                    <span class="text-danger">*</span>
                                                @endif
                                                <small class="d-block text-muted">
                                                    @if ($doc)
                                                        {{ $doc->original_name }} · {{ $doc->formattedSize() }}
                                                    @else
                                                        PDF, JPG, JPEG, PNG · Max 5 MB
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                        @if ($doc)
                                            <div class="student-doc-item__actions">
                                                <a href="{{ route('student.documents.download', $doc) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-download"></i> View
                                                </a>
                                                @if ($canUploadDocuments)
                                                    <form method="POST" action="{{ route('student.documents.destroy', $doc) }}" onsubmit="return confirm('Remove this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @elseif ($canUploadDocuments)
                                            <form method="POST" action="{{ route('student.documents.store') }}" enctype="multipart/form-data" class="student-doc-upload-form">
                                                @csrf
                                                <input type="hidden" name="document_type" value="{{ $type }}">
                                                <label class="student-doc-dropzone">
                                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required>
                                                    <i class="bx bx-upload"></i>
                                                    <span>Choose file or drop here</span>
                                                </label>
                                                <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">Upload</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <p class="small text-muted mb-0">
                                Documents uploaded: <strong>{{ $student->documentsProgressLabel() }}</strong>
                            </p>
                        @elseif ($step['key'] === 'submit')
                            @if ($isSubmitted)
                                <div class="alert alert-success mb-0 py-2">
                                    <i class="bx bx-check-circle me-1"></i>
                                    Your application has been submitted. PDF copies will be emailed to you and your counselor.
                                </div>
                            @elseif ($canSubmit)
                                <form id="studentApplicationSubmitForm" method="POST" action="{{ route('student.application.submit') }}" class="d-none">
                                    @csrf
                                </form>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitApplicationModal">
                                    <i class="bx bx-send me-1"></i> Submit Application
                                </button>
                            @else
                                <div class="alert alert-warning mb-0 py-2">
                                    Complete profile, payment, and document uploads before submitting.
                                    @if (! $student->isProfileComplete())
                                        <a href="{{ route('student.profile.complete') }}">Complete profile</a>
                                    @endif
                                    @if (! $student->hasPaid())
                                        @if (! $student->isProfileComplete()) · @endif
                                        <a href="{{ route('student.payment.index') }}">View payments</a>
                                    @endif
                                    @if (! $student->hasRequiredDocuments())
                                        @if (! $student->isProfileComplete() || ! $student->hasPaid()) · @endif
                                        <a href="#student-documents">Upload documents</a>
                                    @endif
                                </div>
                            @endif
                        @elseif (! $step['done'] && $step['action_url'])
                            <a href="{{ $step['action_url'] }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx {{ $step['icon'] }} me-1"></i> {{ $step['action_label'] }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@if ($canSubmit)
<div class="modal fade" id="submitApplicationModal" tabindex="-1" aria-labelledby="submitApplicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2" id="submitApplicationModalLabel">
                    <i class="bx bx-send text-primary"></i>
                    Submit Application
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2 text-slate-700">Are you sure you want to submit your application?</p>
                <p class="mb-0 text-muted small">PDF copies will be emailed to you and your counselor. You will not be able to edit documents after submission.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="studentApplicationSubmitBtn">
                    <i class="bx bx-check me-1"></i>Submit
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.getElementById('studentApplicationSubmitBtn')?.addEventListener('click', function () {
    document.getElementById('studentApplicationSubmitForm')?.submit();
});

document.querySelectorAll('.student-doc-dropzone input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () {
        const label = input.closest('.student-doc-dropzone');
        const name = input.files && input.files[0] ? input.files[0].name : null;
        if (label && name) {
            label.querySelector('span').textContent = name;
            label.classList.add('has-file');
        }
    });
});
</script>
