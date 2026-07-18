@extends('admin.layouts.app')
@section('title', 'Set Target')
@section('style')
@include('admin.partials.datatables-head')
<style>
    table#targetsTable th, table#targetsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
          @if(session('success'))
            <div class="alert alert-success">
            {{ session('success') }}
            </div>
          @endif
          @if(session('error'))
              <div class="alert alert-danger">
              {{ session('error') }}
              </div>
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
                        <h5 class="">Set Target</h5>
                        <div class="">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add Target
                            </button>
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="targetsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Counselor</th>
                                    <th>Course</th>
                                    <th>Academic Year</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($targets as $target)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $target->counselor->name ?? '—' }}</td>
                                    <td>{{ $target->course->name ?? '—' }}</td>
                                    <td>{{ $target->academicYear->name ?? '—' }}</td>
                                    <td>₹{{ number_format((float) $target->amount, 2) }}</td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-icon btn-outline-warning edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                data-id="{{ $target->id }}"
                                                data-counselor-id="{{ $target->counselor_id }}"
                                                data-course-id="{{ $target->course_id }}"
                                                data-academic-year-id="{{ $target->academic_year_id }}"
                                                data-amount="{{ $target->amount }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.set-target.destroy', $target->id) }}"
                                              method="POST"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-icon btn-outline-danger"
                                                    data-confirm-delete="Are you sure you want to delete this target?">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.settings.set-target.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Target</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Counselor</label>
                        <select name="counselor_id" class="form-select" required>
                            <option value="">Select Counselor</option>
                            @foreach($counselors as $counselor)
                                <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected($year->is_active)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Target</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Counselor</label>
                        <select name="counselor_id" id="edit_counselor_id" class="form-select" required>
                            <option value="">Select Counselor</option>
                            @foreach($counselors as $counselor)
                                <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" id="edit_course_id" class="form-select" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" id="edit_academic_year_id" class="form-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#targetsTable');

        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            $('#editForm').attr('action', `/admin/settings/set-target/${id}`);
            $('#edit_counselor_id').val($(this).data('counselor-id'));
            $('#edit_course_id').val($(this).data('course-id'));
            $('#edit_academic_year_id').val($(this).data('academic-year-id'));
            $('#edit_amount').val($(this).data('amount'));
        });
    });
</script>
@endsection
