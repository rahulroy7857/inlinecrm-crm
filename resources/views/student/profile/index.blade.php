@extends('student.layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  @include('student.partials.alerts')

  <div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
      <h5 class="mb-0">My Profile</h5>
      <span class="badge bg-label-primary">{{ $student->lead_ref }}</span>
    </div>
    <div class="card-body mt-3">
      <form method="POST" action="{{ route('student.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $student->name) }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $student->email }}" disabled>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Mobile</label>
            <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $student->mobile) }}" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Country</label>
            <input type="text" class="form-control" name="country" value="{{ old('country', $student->country) }}">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">State</label>
            <input type="text" class="form-control" name="state" value="{{ old('state', $student->state) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Course</label>
            <input type="text" class="form-control" value="{{ $student->course?->name ?? 'N/A' }}" disabled>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Gender</label>
            <select class="form-control" name="gender">
              <option value="">Select</option>
              @foreach(['Male','Female','Other'] as $g)
                <option value="{{ $g }}" {{ old('gender', $student->gender) == $g ? 'selected' : '' }}>{{ $g }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="dob" value="{{ old('dob', $student->dob?->format('Y-m-d')) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Aadhar Number</label>
            <input type="text" class="form-control" name="aadhar" value="{{ old('aadhar', $student->aadhar) }}">
          </div>
        </div>

        <h6 class="mt-2 mb-3">Parent / Guardian Details</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Father's Name</label>
            <input type="text" class="form-control" name="father_name" value="{{ old('father_name', $student->father_name) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Father's Occupation</label>
            <input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation', $student->father_occupation) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Mother's Name</label>
            <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Mother's Occupation</label>
            <input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation', $student->mother_occupation) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Guardian's Name</label>
            <input type="text" class="form-control" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Relation</label>
            <input type="text" class="form-control" name="relation" value="{{ old('relation', $student->relation) }}">
          </div>
        </div>

        <h6 class="mt-2 mb-3">Address</h6>
        <div class="row">
          <div class="col-md-12 mb-3">
            <label class="form-label">Present Address</label>
            <textarea class="form-control" name="present_address" rows="2">{{ old('present_address', $student->present_address) }}</textarea>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">City</label>
            <input type="text" class="form-control" name="present_city" value="{{ old('present_city', $student->present_city) }}">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">PIN Code</label>
            <input type="text" class="form-control" name="present_pin" value="{{ old('present_pin', $student->present_pin) }}">
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Permanent Address</label>
            <textarea class="form-control" name="permanent_address" rows="2">{{ old('permanent_address', $student->permanent_address) }}</textarea>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Profile</button>
      </form>
    </div>
  </div>
</div>
@endsection
