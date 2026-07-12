@extends('admin.layouts.app')
@section('title', 'Account Breaks')
@section('style')
@include('admin.partials.datatables-head')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($pendingRequests->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Pending Break Requests ({{ $pendingRequests->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Break Type</th>
                            <th>Requested At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                        <tr>
                            <td>{{ $request->account->name }}</td>
                            <td>{{ $request->label }}</td>
                            <td>{{ $request->requested_at?->format('d-m-Y h:i A') }}</td>
                            <td class="d-flex flex-wrap gap-2">
                                <button type="button"
                                        class="btn btn-sm btn-success approve-break-btn"
                                        data-id="{{ $request->id }}"
                                        data-name="{{ $request->account->name }}"
                                        data-label="{{ $request->label }}">
                                    Approve
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger reject-break-btn"
                                        data-id="{{ $request->id }}"
                                        data-name="{{ $request->account->name }}"
                                        data-label="{{ $request->label }}">
                                    Reject
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Break Type Settings</h5>
            <p class="mb-0 small text-muted">Set duration for each break. Types without duration require admin approval before Account can start the break.</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.account-breaks.update') }}">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table crm-table">
                        <thead>
                            <tr>
                                <th>Break Type</th>
                                <th>Label</th>
                                <th>Duration (minutes)</th>
                                <th>Requires Admin Approval</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $index => $setting)
                            <tr>
                                <td>
                                    <input type="hidden" name="settings[{{ $index }}][type]" value="{{ $setting->type }}">
                                    <code>{{ $setting->type }}</code>
                                </td>
                                <td>
                                    <input type="text" name="settings[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $setting->label }}" required>
                                </td>
                                <td>
                                    <input type="number" name="settings[{{ $index }}][duration_minutes]" class="form-control form-control-sm" min="1" max="480" value="{{ $setting->duration_minutes }}" placeholder="Empty = no limit">
                                </td>
                                <td>
                                    <input type="hidden" name="settings[{{ $index }}][requires_admin_approval]" value="0">
                                    <input type="checkbox" class="form-check-input" name="settings[{{ $index }}][requires_admin_approval]" value="1" {{ $setting->requires_admin_approval ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <input type="hidden" name="settings[{{ $index }}][is_active]" value="0">
                                    <input type="checkbox" class="form-check-input" name="settings[{{ $index }}][is_active]" value="1" {{ $setting->is_active ? 'checked' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveBreakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="approveBreakForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Approve Break Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Approve <strong id="approveBreakLabel"></strong> for <strong id="approveBreakName"></strong>?</p>
                <div class="mb-0">
                    <label class="form-label">Duration (minutes) <span class="text-muted">optional</span></label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" max="480" placeholder="Leave empty for open-ended break">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Approve Break</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="rejectBreakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="rejectBreakForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Reject Break Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Reject <strong id="rejectBreakLabel"></strong> for <strong id="rejectBreakName"></strong>?</p>
                <div class="mb-0">
                    <label class="form-label">Reason <span class="text-muted">optional</span></label>
                    <textarea name="rejected_reason" class="form-control" rows="3" placeholder="Reason for rejection"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.approve-break-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('approveBreakName').textContent = btn.dataset.name;
            document.getElementById('approveBreakLabel').textContent = btn.dataset.label;
            document.getElementById('approveBreakForm').action =
                "{{ route('admin.settings.account-breaks.approve', ':id') }}".replace(':id', btn.dataset.id);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('approveBreakModal')).show();
        });
    });

    document.querySelectorAll('.reject-break-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('rejectBreakName').textContent = btn.dataset.name;
            document.getElementById('rejectBreakLabel').textContent = btn.dataset.label;
            document.getElementById('rejectBreakForm').action =
                "{{ route('admin.settings.account-breaks.reject', ':id') }}".replace(':id', btn.dataset.id);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectBreakModal')).show();
        });
    });
});
</script>
@endsection
