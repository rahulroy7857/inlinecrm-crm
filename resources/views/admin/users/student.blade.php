@extends('admin.layouts.app')
@section('title', 'Student Users')
@section('style')
@include('admin.partials.datatables-head')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Portal Users</h5>
            <a href="{{ route('student.login') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-link-external me-1"></i> Student Login
            </a>
        </div>
        <div class="card-body mt-3">
            <div class="table-responsive">
                <table id="studentsTable" class="table crm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lead ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Course</th>
                            <th>Application</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($student->lead_ref)
                                    <a href="{{ student_registration_url($student->lead_ref) }}" target="_blank" title="Registration link">
                                        {{ $student->lead_ref }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->mobile }}</td>
                            <td>{{ $student->course?->name ?? '—' }}</td>
                            <td><span class="badge bg-label-primary">{{ $student->applicationStatusLabel() }}</span></td>
                            <td>
                                <span class="badge bg-{{ $student->hasAllFeesPaid() ? 'success' : ($student->hasFeesSet() ? 'warning' : 'secondary') }}">
                                    {{ $student->feeCompletionStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $student->status ? 'success' : 'danger' }}">
                                    {{ $student->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if($student->lead_ref)
                                <a href="{{ student_registration_url($student->lead_ref) }}" target="_blank"
                                   class="btn btn-icon btn-outline-primary" title="Open registration link">
                                    <i class="bx bx-link-external"></i>
                                </a>
                                @endif
                                <form action="{{ route('admin.users.student.destroy', $student->id) }}" method="POST" class="d-inline"
                                      data-confirm-delete="Delete this student account?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No student accounts yet. Share a registration link from a lead profile.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
           
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.partials.datatables-scripts')
<script>
$(function() {
    initCrmDataTable('#studentsTable');
});
</script>
@endsection
