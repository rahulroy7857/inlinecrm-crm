@extends('counselor.layouts.app')
@section('title', 'Lead Profile')
@section('style')   
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .timeline {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        width: 3px;
        height: 100%;
        background: linear-gradient(135deg, #d43661 0%, #764ba2 100%);
        border-radius: 2px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        padding-left: 50px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 5px;
        left: 12px;
        width: 16px;
        height: 16px;
        background: linear-gradient(135deg, #d43661 0%, #764ba2 100%);
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 15px rgba(212, 54, 97, 0.3);
        z-index: 1;
    }
    .timeline-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 8px;
        font-weight: 500;
    }
    .timeline-content {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #d43661;
    }
    .timeline-content h6 {
        font-size: 1.1rem;
        margin-bottom: 8px;
        color: #495057;
        font-weight: 600;
    }
    .timeline-content p {
        margin: 0;
        color: #6c757d;
        line-height: 1.6;
    }
    /* .timeline {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        width: 2px;
        height: 100%;
        background-color: #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 40px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 14px;
        width: 12px;
        height: 12px;
        background-color: #0d6efd;
        border-radius: 50%;
        border: 2px solid #fff;
        z-index: 1;
    }
    .timeline-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .timeline-content h6 {
        font-size: 1rem;
        margin-bottom: 5px;
    }
    .timeline-content p {
        margin: 0;
        color: #6c757d;
    } */
    .editable-buttons .btn-default{
        background-color: #e44444;
        color: #fff;
    }
    .editable-buttons .btn {
        padding: 5px 10px;
    }
    /* Fix tick (✓) and cross (✗) icons for Bootstrap 5 */
    .editable-buttons button[type="submit"]::before {
        content: "✓";
        font-family: Arial;
    }
    .editable-buttons button[type="button"]::before {
        content: "X";
        font-family: Arial;
    }
    .editable-input .form-control {
        padding: 0.3375rem 0.675rem;
        font-size: 0.8375rem;
        padding-right: 0px !important;
    }
    .editable {
        padding-left: 5px;
        color: #566a7f;
    }
    .editableform .form-control {
        min-width: 150px;
    }
    .editableform .combodate .form-control {
        min-width: 50px;
        max-width: 50px;
        display: inline-block;
    }
    .editableform .editable-input {
        min-width: 150px;
    }
    .editableform select {
        width: 100% !important;
    }
    .editableform .form-control.select2 {
        min-width: 100% !important;
    }
    .editableform .select2-container {
        min-width: 100% !important;
    }
    .table-first-section tbody tr {
        line-height:30px;
    }
    .table-third-section tbody tr {
        line-height:30px;
    }
    .select2-container--default .select2-selection--single {
        height: 33px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 21px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .not-textarea .editable-container {
        width: 700px !important;
    }
    .not-textarea .input-large{
        width: 700px !important;
    }
    .terms-conditions-editable .editable-container {
        width: 538px !important;
    }
    .terms-conditions-editable .input-large {
        width: 538px !important;
    }
    input[type="datetime-local"] {
        position: relative;
        z-index: 1;
    }

    .holiday-warning {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    input[type="datetime-local"]:invalid {
        border-color: #dc3545;
    }

    input[type="datetime-local"]:invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y lead-profile-page px-3 px-lg-4">
    <div class="lead-profile-layout">
        <div class="lead-profile-sidebar-col">
            <div class="card lead-profile-sidebar">
            <div class="card-body">
                <div class="lead-profile-header">
                <div class="dropdown">
                <button
                  class="btn btn-sm btn-outline-secondary border-0 p-1"
                  type="button"
                  id="cardOpt3"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  <i class="bx bx-dots-vertical-rounded text-lg"></i>
                </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#followupModal">Add Contact Log</a>
                        <!-- <form action="{{ route('counselor.lead.destroy', $lead->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</button>
                        </form> -->
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer</a>
                        @if(!in_array($lead->status, ['Application', 'Reservation', 'Admission', 'Cancelled']))
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#applicationModal">Application</a>
                        @endif
                        @if(!in_array($lead->status, ['Reservation', 'Admission', 'Cancelled']))
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#reservationModal">Reservation</a>
                        @endif
                        @if(!in_array($lead->status, ['Admission', 'Cancelled']))
                            <!-- <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#admissionModal">Admission</a> -->
                        @endif
                        @if(in_array($lead->status, ['Reservation', 'Admission']))
                            <!-- <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel</a> -->
                        @endif
                    </div>
                </div>

                <div class="lead-photo-wrap">
                    <form id="photoForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @php
                            $leadPhotoUrl = ($lead->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists('leads/' . $lead->photo))
                                ? asset('storage/leads/' . $lead->photo)
                                : null;
                        @endphp
                        @if($leadPhotoUrl)
                            <img src="{{ $leadPhotoUrl }}" alt="Lead Photo" class="mb-0" id="leadPhoto">
                        @else
                            <div class="lead-photo-placeholder mb-0" id="leadPhotoPlaceholder" aria-hidden="true">
                                <i class="bx bx-user"></i>
                            </div>
                            <img src="" alt="Lead Photo" class="mb-0 d-none" id="leadPhoto">
                        @endif
                        <label for="photoInput">
                            <i class="bx bx-camera text-sm"></i>
                            <input type="file" 
                                id="photoInput" 
                                name="photo"
                                class="hidden" 
                                accept="image/*"
                                onchange="updatePhoto(this)">
                        </label>
                    </form>
                </div>

                <h5 class="mb-2 text-lg font-semibold text-slate-900">{{ $lead->name ?? 'Unknown' }}</h5>
                <div class="lead-meta">
                    <p class="mb-0"><strong>Lead ID:</strong> {{ $lead->lead_id ?? 'Unknown' }}
                        @if($lead->lead_id)
                        <a href="{{ $lead->student ? route('student.login') : student_registration_url($lead->lead_id) }}"
                           class="btn btn-sm btn-outline-primary lead-register-btn ms-1"
                           title="{{ $lead->student ? 'Open student login' : 'Open student registration' }}">
                            <i class="bx bx-link-external" aria-hidden="true"></i>
                        </a>
                        @endif
                    </p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-{!! \App\Helpers\LeadStatus::getColor($lead->status) !!}">{{ $lead->status }}</span></p>
                    <p class="mb-0"><strong>Counselor:</strong> {{ $lead->counselor->name ?? 'Not Assigned' }}</p>
                    <p class="mb-0"><strong>Next FL:</strong> {{ $lead->next_follow_up ?? 'Not Scheduled' }}</p>
                </div>
                </div>

                <div class="lead-quick-actions">
                    <div class="flex flex-wrap justify-center gap-2">
                    <a href="#" class="btn btn-primary lead-action-btn" data-bs-toggle="modal" data-bs-target="#followupModal" title="Follow Up" aria-label="Follow Up">
                        <i class="bx bx-phone" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="btn btn-warning lead-action-btn" data-bs-toggle="modal" data-bs-target="#transferModal" title="Transfer" aria-label="Transfer">
                        <i class="bx bx-transfer-alt" aria-hidden="true"></i>
                    </a>
                    @if(!in_array($lead->status, ['Application', 'Reservation', 'Admission', 'Cancelled']))
                    <a href="#" class="btn btn-info lead-action-btn" data-bs-toggle="modal" data-bs-target="#applicationModal" title="Application" aria-label="Application">
                        <i class="bx bx-file" aria-hidden="true"></i>
                    </a>
                    @endif
                    @if(!in_array($lead->status, ['Cancelled', 'Admission']))
                    <!-- <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#admissionModal" title="Admission">
                        <i class="bx bx-building"></i>
                    </a> -->
                    @endif
                    @if(in_array($lead->status, ['Reservation', 'Admission']))
                    @if($lead->status != 'Cancelled')
                    <!-- <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" title="Cancel">
                        <i class="bx bx-x"></i>
                    </a> -->
                    @endif
                    @endif
                    </div>
                </div>

                <nav class="lead-profile-nav" aria-label="Lead sections">
                        <div class="nav flex-column" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" href="#overview" role="tab" onclick="activateMenu(this); showContent('overview')">
                                <i class="bx bx-user"></i><span>Overview</span>
                            </a>
                            <a class="nav-link" href="#contacts" role="tab" onclick="activateMenu(this); showContent('contacts')">
                                <i class="bx bx-phone"></i><span>Contact Details</span>
                            </a>
                            <a class="nav-link" href="#timeline" role="tab" onclick="activateMenu(this); showContent('timeline')">
                                <i class="bx bx-time"></i><span>Timeline</span>
                            </a>
                            <a class="nav-link" href="#call-log" role="tab" onclick="activateMenu(this); showContent('call-log')">
                                <i class="bx bx-log-in"></i><span>Contact Log</span>
                            </a>
                            <a class="nav-link" href="#education" role="tab" onclick="activateMenu(this); showContent('education')">
                                <i class="bx bx-book"></i><span>Education</span>
                            </a>
                            <a class="nav-link" href="#exams" role="tab" onclick="activateMenu(this); showContent('exams')">
                                <i class="bx bx-test-tube"></i><span>Exams</span>
                            </a>
                            @if($lead->lead_id)
                            <a class="nav-link lead-student-link" href="{{ $lead->student ? route('student.login') : student_registration_url($lead->lead_id) }}" target="_blank" rel="noopener"
                               title="{{ $lead->student ? 'Student registered — open login' : 'Open student registration' }}">
                                <i class="bx bx-user"></i><span>Student</span>
                                @if($lead->student)
                                    <span class="badge bg-success ms-1" style="font-size:10px;">Registered</span>
                                @endif
                                <i class="bx bx-link-external ms-auto small opacity-75"></i>
                            </a>
                            @endif
                        </div>
                </nav>
            </div>
            </div>
        </div>
        <div class="lead-profile-content-col">
            <div class="card lead-content-card" id="overview-card">
                <div class="card-header border-bottom">
                    <h5><i class="bx bx-user"></i>Lead Overview</h5>
                </div>
                <div class="card-body lead-content-body">
                    <div class="overview-grid mb-4">
                            <div class="overview-section">
                                <div class="section-header">
                                    <span><i class="bx bx-info-circle me-2"></i>Lead Information</span>
                                </div>                                
                                <table class="table table-borderless table-first-section w-100">
                                    <tbody>
                                        <tr>
                                            <th style="width: 17%;">Name</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-name="name" 
                                                data-type="text" 
                                                data-pk="{{ $lead->id }}" 
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Name">{{ $lead->name }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Created On</th>
                                            <td style="display: flex; align-items: center;">: 
                                                 <span> {{ $lead->created_at->format('d M Y, h:i A') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Source</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                    class="editable"
                                                    data-name="source_id"
                                                    data-type="select2"
                                                    data-pk="{{ $lead->id }}"
                                                    data-source="{{ json_encode($sources) }}"
                                                    data-value="{{ $lead->source_id }}"
                                                    data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                    data-title="Edit Source"
                                                    data-placeholder="Search Source"
                                                >{{ $lead->source->name ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Assigned To</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-name="counselor_id" 
                                                data-type="select2"
                                                data-type="select" 
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($counselors) }}"
                                                data-value="{{ $lead->counselor_id }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Counselor">{{ $lead->counselor->name ?? 'Not Assigned' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Academic Year</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-name="academic_year_id" 
                                                data-type="select" 
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($academicYears) }}"
                                                data-value="{{ $lead->academic_year_id }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Academic Year">{{ $lead->academicYear->name ?? 'Not Available' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Course</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-type="select2"
                                                data-name="course_id" 
                                                data-type="select" 
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($courses) }}"
                                                data-value="{{ $lead->course_id }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Course">{{ $lead->course->name ?? 'Not Available' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Specialization</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-name="specialization" 
                                                data-type="text" 
                                                data-pk="{{ $lead->id }}" 
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Specialization">{{ $lead->specialization ?? 'Not Available' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">College</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-type="select2"
                                                data-name="college_id" 
                                                data-type="select" 
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($colleges) }}"
                                                data-value="{{ $lead->college_id }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit College">{{ $lead->college->name ?? 'Not Available' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">Country</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                class="editable" 
                                                data-type="select2"
                                                data-name="country" 
                                                data-type="select"
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($countries) }}"
                                                data-value="{{ $lead->country }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit Country">{{ $lead->country ?? 'Not Available' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 17%;">State</th>
                                            <td style="display: flex; align-items: center;">:<a href="#" 
                                                data-type="select2"
                                                class="editable" 
                                                data-name="state" 
                                                data-type="select"
                                                data-pk="{{ $lead->id }}" 
                                                data-source="{{ json_encode($states) }}"
                                                data-value="{{ $lead->state }}"
                                                data-url="{{ route('counselor.leads.update', $lead->id) }}" 
                                                data-title="Edit State">{{ $lead->state ?? 'Not Available' }}</a></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="overview-section">
                                <div class="section-header">
                                    <span><i class="bx bx-group me-2"></i>Parent Details</span>
                                </div>                                
                                <table class="table table-borderless w-100">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">Father's Name</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="father_name"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Father's Name"
                                                   data-placement="right">{{ $lead->father_name ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Father's Occupation</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="father_occupation"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Father's Occupation"
                                                   data-placement="right">{{ $lead->father_occupation ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Mother's Name</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="mother_name"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Mother's Name"
                                                   data-placement="right">{{ $lead->mother_name ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Mother's Occupation</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="mother_occupation"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Mother's Occupation"
                                                   data-placement="right">{{ $lead->mother_occupation ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Guardian's Name</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="guardian_name"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Guardian's Name"
                                                   data-placement="right">{{ $lead->guardian_name ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Relation</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="relation"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Relation"
                                                   data-placement="right">{{ $lead->relation ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="overview-section">
                                <div class="section-header">
                                    <span><i class="bx bx-detail me-2"></i>Other Details</span>
                                </div>
                                <table class="table table-borderless table-third-section w-100">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">Gender</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="gender"
                                                   data-type="select"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Gender"
                                                   data-source='[{"value":"Male","text":"Male"},{"value":"Female","text":"Female"},{"value":"Other","text":"Other"}]'
                                                   data-value="{{ $lead->gender ?? '' }}"
                                                   data-placement="right">{{ $lead->gender ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">DOB</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                    class="editable"
                                                    data-name="dob"
                                                    data-type="combodate"
                                                    data-format="DD-MM-YYYY"
                                                    data-viewformat="DD-MM-YYYY"
                                                    data-template="DD / MM / YYYY"
                                                    data-combodate='{"minYear":1970,"maxYear":{{ date("Y") }}}'
                                                    data-pk="{{ $lead->id }}"
                                                    data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                    data-title="Select Date of Birth"
                                                    data-value="{{ $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('d-m-Y') : '' }}"
                                                    data-placement="right"
                                                    style="display: inline-block; min-width: 50px; vertical-align: middle;"
                                                >
                                                    {{ $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('d-m-Y') : '' }}
                                                </a>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 30%;">Aadhar</th>
                                            <td style="display: flex; align-items: center;">:
                                                <a href="#"
                                                   class="editable"
                                                   data-name="aadhar"
                                                   data-type="text"
                                                   data-pk="{{ $lead->id }}"
                                                   data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                   data-title="Edit Aadhar"
                                                   data-placement="right">{{ $lead->aadhar ?? 'Not Available' }}</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="row mb-4 g-0">
                        <div class="col-12">
                            <div class="section-header">
                                <span><i class="bx bx-note me-2"></i>Notes</span>
                            </div>                            
                            <p class="text-muted not-textarea" style="line-height: 1.8;margin-left: 19px;">
                                <a href="#"
                                    class="editable editable-textarea"
                                    data-name="notes"
                                    data-type="textarea"
                                    data-rows="3"
                                    data-pk="{{ $lead->id }}"
                                    data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                    data-title="Edit Notes"
                                    data-placement="right"
                                    style="display: block; width: 100%;"
                                >{{ $lead->notes ?? 'No notes available for this lead' }}</a>
                                
                            </p>
                        </div>
                    </div>
                    @if($lead->application_date != '')
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3" style="background-color: #e2e2e2; padding: 10px; border-radius: 5px;padding-left: 19px;color: #000 !important;">Application Details</h6>                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Date</th>
                                                <td style="display: flex; align-items: center;">:
                                                    
                                                    <a href="#"
                                                        class="editable"
                                                        data-name="application_date"
                                                        data-type="combodate"
                                                        data-format="YYYY-M-DD"
                                                        data-viewformat="YYYY-M-DD"
                                                        data-template="YYYY / M / D"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Select Application Date"
                                                        data-value="{{ $lead->application_date ? $lead->application_date : '' }}"
                                                        data-placement="right"
                                                        data-combodate='{"minYear":2024,"maxYear":{{ date("Y") }}}'
                                                        style="display: inline-block; min-width: 50px; vertical-align: middle;"
                                                    >
                                                         {{ $lead->application_date ? $lead->application_date : '' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>            
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Application</th>
                                                <td style="display: flex; align-items: center;">:
                                                    <a href="#">View
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>            
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width:15%">Note</th>
                                                <td style="display: flex; align-items: center;" class="terms-conditions-editable">
                                                    :<a href="#"
                                                        class="editable"
                                                        data-name="application_note"
                                                        data-type="textarea"
                                                        data-rows="3"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Edit Application Note"
                                                        data-placement="right"
                                                    >{{ $lead->application_note ?? 'Not Available' }}</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($lead->reservation_note != '')
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3" style="background-color: #e2e2e2; padding: 10px; border-radius: 5px;padding-left: 19px;color: #000 !important;">Reservation Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Date</th>
                                                <td style="display: flex; align-items: center;">:
                                                    
                                                    <a href="#"
                                                        class="editable"
                                                        data-name="reservation_note"
                                                        data-type="combodate"
                                                        data-format="YYYY-M-DD"
                                                        data-viewformat="YYYY-M-DD"
                                                        data-template="YYYY / M / D"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Select Reservation Date"
                                                        data-value="{{ $lead->reservation_note ? $lead->reservation_note : '' }}"
                                                        data-placement="right"
                                                        data-combodate='{"minYear":2024,"maxYear":{{ date("Y") }}}'
                                                        style="display: inline-block; min-width: 50px; vertical-align: middle;"
                                                    >
                                                         {{ $lead->reservation_note ? $lead->reservation_note : '' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>            
                                </div>
                               
                                <div class="col-md-12">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width:15%">Note</th>
                                                <td style="display: flex; align-items: center;" class="terms-conditions-editable">
                                                    :<a href="#"
                                                        class="editable"
                                                        data-name="application_note"
                                                        data-type="textarea"
                                                        data-rows="3"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Edit Application Note"
                                                        data-placement="right"
                                                    >{{ $lead->application_note ?? 'Not Available' }}</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($lead->admission_date != '')
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3" style="background-color: #e2e2e2; padding: 10px; border-radius: 5px;padding-left: 19px;color: #000 !important;">Admission Details</h6>                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Date</th>
                                                <td style="display: flex; align-items: center;">:
                                                    {{ $lead->admission_date ? $lead->admission_date : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 30%;">College</th>
                                                <td style="display: flex; align-items: center;">:
                                                    {{ $lead->college->name ?? 'Not Available' }}
                                                </td>
                                            </tr>
                                            
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Admission No</th>
                                                <td style="display: flex; align-items: center;">:
                                                    
                                                        {{ $lead->admission_no ?? 'Not Available' }}
                                                   
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 30%;">Course</th>
                                                <td style="display: flex; align-items: center;">:
                                                    {{ $lead->course->name ?? 'Not Available' }}
                                                </td>
                                            </tr>
                                           
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width:16%">Terms and Conditions</th>
                                                <td style="display: flex; align-items: center;" class="terms-conditions-editable">
                                                    :<a href="#"
                                                        class="editable"
                                                        data-name="terms_and_conditions"
                                                        data-type="textarea"
                                                        data-rows="3"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Edit Terms and Conditions"
                                                        data-placement="right"
                                                    >{{ $lead->terms_and_conditions ?? 'Not Available' }}</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($lead->cancel_date != '')
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3" style="background-color: #e2e2e2; padding: 10px; border-radius: 5px;padding-left: 19px;color: #000 !important;">Cancellation Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Date</th>
                                                <td style="display: flex; align-items: center;">:
                                                    
                                                    <a href="#"
                                                        class="editable"
                                                        data-name="cancel_date"
                                                        data-type="combodate"
                                                        data-format="YYYY-M-DD"
                                                        data-viewformat="YYYY-M-DD"
                                                        data-template="YYYY / M / D"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Select Cancellation Date"
                                                        data-value="{{ $lead->cancel_date ? $lead->cancel_date : '' }}"
                                                        data-placement="right"
                                                        data-combodate='{"minYear":2024,"maxYear":{{ date("Y") }}}'
                                                        style="display: inline-block; min-width: 50px; vertical-align: middle;"
                                                    >
                                                         {{ $lead->cancel_date ? $lead->cancel_date : '' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>            
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Reason</th>
                                                <td style="display: flex; align-items: center;">:
                                                    <a href="#"
                                                        class="editable"
                                                        data-name="cancel_reason"
                                                        data-type="select"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Edit Cancellation Reason"
                                                        data-source='[
                                                             {"value":"Change of Mind","text":"Change of Mind"},
                                                             {"value":"Financial Issues","text":"Financial Issues"},
                                                             {"value":"Got Better Option","text":"Got Better Option"},
                                                             {"value":"Personal Reason","text":"Personal Reason"},
                                                             {"value":"Other","text":"Other"}
                                                        ]'
                                                        data-value="{{ $lead->cancel_reason ?? '' }}"
                                                        data-placement="right"
                                                    >
                                                         {{ $lead->cancel_reason ?? 'Not Available' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>            
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width:15%">Note</th>
                                                <td style="display: flex; align-items: center;" class="terms-conditions-editable">
                                                    :<a href="#"
                                                        class="editable"
                                                        data-name="cancel_note"
                                                        data-type="textarea"
                                                        data-rows="3"
                                                        data-pk="{{ $lead->id }}"
                                                        data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                                        data-title="Edit Cancellation Note"
                                                        data-placement="right"
                                                    >{{ $lead->cancel_note ?? 'Not Available' }}</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card lead-content-card" id="contacts-card" style="display: none;">
                <div class="card-header border-bottom">
                    <h5><i class="bx bx-phone me-2"></i>Contact Details</h5>
                </div>
                <div class="card-body lead-content-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-header">
                                <span><i class="bx bx-phone me-2"></i>Phone Numbers</span>
                            </div>                            
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;">Mobile</th>
                                        <td style="display: flex; align-items: center;">:
                                            @if($lead->mobile)
                                                <span style="margin-left: 5px;">{{ lead_phone_prefix($lead->country) }}</span>
                                            @endif
                                            <a href="#"
                                               class="editable"
                                               data-name="mobile"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Mobile"
                                               data-placement="right"
                                               data-value="{{ $lead->mobile ?? '' }}"
                                               style="{{ $lead->mobile ? '' : 'margin-left: 5px;' }}">
                                                {{ $lead->mobile ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Alternative</th>
                                        <td style="display: flex; align-items: center;">:
                                            @if($lead->alternative_mobile)
                                                <span style="margin-left: 5px;">{{ lead_phone_prefix($lead->country) }}</span>
                                            @endif
                                            <a href="#"
                                               class="editable"
                                               data-name="alternative_mobile"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Alternative Mobile"
                                               data-value="{{ $lead->alternative_mobile ?? '' }}"
                                               data-placement="right"
                                               style="{{ $lead->alternative_mobile ? '' : 'margin-left: 5px;' }}">
                                                {{ $lead->alternative_mobile ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Father</th>
                                        <td style="display: flex; align-items: center;">:
                                            @if($lead->father_mobile)
                                                <span style="margin-left: 5px;">{{ lead_phone_prefix($lead->country) }}</span>
                                            @endif
                                            <a href="#"
                                               class="editable"
                                               data-name="father_mobile"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Father's Contact"
                                               data-value="{{ $lead->father_mobile ?? '' }}"
                                               data-placement="right"
                                               style="{{ $lead->father_mobile ? '' : 'margin-left: 5px;' }}">
                                                {{ $lead->father_mobile ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Mother</th>
                                        <td style="display: flex; align-items: center;">:
                                            @if($lead->mother_mobile)
                                                <span style="margin-left: 5px;">{{ lead_phone_prefix($lead->country) }}</span>
                                            @endif
                                            <a href="#"
                                               class="editable"
                                               data-name="mother_mobile"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Mother's Contact"
                                               data-value="{{ $lead->mother_mobile ?? '' }}"
                                               data-placement="right"
                                               style="{{ $lead->mother_mobile ? '' : 'margin-left: 5px;' }}">
                                                {{ $lead->mother_mobile ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Guardian</th>
                                        <td style="display: flex; align-items: center;">:
                                            @if($lead->guardian_mobile)
                                                <span style="margin-left: 5px;">{{ lead_phone_prefix($lead->country) }}</span>
                                            @endif
                                            <a href="#"
                                               class="editable"
                                               data-name="guardian_mobile"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Guardian's Contact"
                                               data-value="{{ $lead->guardian_mobile ?? '' }}"
                                               data-placement="right"
                                               style="{{ $lead->guardian_mobile ? '' : 'margin-left: 5px;' }}">
                                                {{ $lead->guardian_mobile ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="section-header">
                                <span><i class="bx bx-envelope me-2"></i>Email Addresses</span>
                            </div>                            
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;">Personal</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="personal_email"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Personal Email"
                                               data-placement="right"
                                               data-value="{{ $lead->personal_email ?? '' }}">
                                                {{ $lead->personal_email ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Father</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="father_email"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Father's Email"
                                               data-placement="right"
                                               data-value="{{ $lead->father_email ?? '' }}">
                                                {{ $lead->father_email ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Mother</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="mother_email"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Mother's Email"
                                               data-placement="right"
                                               data-value="{{ $lead->mother_email ?? '' }}">
                                                {{ $lead->mother_email ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Guardian</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="guardian_email"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Guardian's Email"
                                               data-placement="right"
                                               data-value="{{ $lead->guardian_email ?? '' }}">
                                                {{ $lead->guardian_email ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="section-header">
                                <span><i class="bx bx-home me-2"></i>Present Address</span>
                            </div>                            
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;">Address</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_address"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Present Address"
                                               data-value="{{ $lead->present_address ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_address ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">City</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_city"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit City"
                                               data-value="{{ $lead->present_city ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_city ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Place</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_place"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Place"
                                               data-value="{{ $lead->present_place ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_place ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">State</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_state"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit State"
                                               data-value="{{ $lead->present_state ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_state ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Country</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_country"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Country"
                                               data-value="{{ $lead->present_country ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_country ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Pin</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="present_pin"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Pin"
                                               data-value="{{ $lead->present_pin ?? '' }}"
                                               data-placement="right">
                                                {{ $lead->present_pin ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="section-header">
                                <span><i class="bx bx-home me-2"></i>Permanent Address</span>
                            </div>                            
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 20%;">Address</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_address"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Permanent Address"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_address ?? '' }}">
                                                {{ $lead->permanent_address ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">City</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_city"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit City"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_city ?? '' }}">
                                                {{ $lead->permanent_city ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Place</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_place"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Place"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_place ?? '' }}">
                                                {{ $lead->permanent_place ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">State</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_state"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit State"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_state ?? '' }}">
                                                {{ $lead->permanent_state ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Country</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_country"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Country"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_country ?? '' }}">
                                                {{ $lead->permanent_country ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 20%;">Pin</th>
                                        <td style="display: flex; align-items: center;">:
                                            <a href="#"
                                               class="editable"
                                               data-name="permanent_pin"
                                               data-type="text"
                                               data-pk="{{ $lead->id }}"
                                               data-url="{{ route('counselor.leads.update', $lead->id) }}"
                                               data-title="Edit Pin"
                                               data-placement="right"
                                               data-value="{{ $lead->permanent_pin ?? '' }}">
                                                {{ $lead->permanent_pin ?? 'Not Available' }}
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card lead-content-card" id="timeline-card" style="display: none;">
                <div class="card-header border-bottom">
                    <h5><i class="bx bx-time me-2"></i>Timeline</h5>
                </div>
                <div class="card-body lead-content-body">
                    <div class="section-header">
                        <span><i class="bx bx-time me-2"></i>Lead Timeline</span>
                    </div>                    
                    <ul class="timeline">
                        @foreach(($lead->timeline ?? []) as $event)
                            <li class="timeline-item">
                                <span class="timeline-date">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y, h:i A') }}</span>
                                <div class="timeline-content">
                                    <h6>{{ $event->title }}</h6>
                                    <p>{{ $event->description }}</p>
                                    <small class="text-muted">
                                        @if($event->user)
                                            Performed by: {{ $event->user->name }}
                                        @endif
                                        <span class="badge bg-{{ $event->event_type === 'system' ? 'info' : 'primary' }}">
                                            {{ ucfirst($event->event_type) }}
                                        </span>
                                    </small>
                                </div>
                            </li>
                        @endforeach
                        @if($lead->timeline->isEmpty())
                            <li class="timeline-item">
                                <span class="timeline-date">{{ now()->format('d M Y') }}</span>
                                <div class="timeline-content">
                                    <h6>No Timeline Events</h6>
                                    <p>No timeline events available for this lead.</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card lead-content-card" id="call-log-card" style="display: none;">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bx bx-log-in me-2"></i>Contact Log</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#followupModal">Add Contact Log</button>
                    </div>
                </div>
                <div class="card-body lead-content-body">
                    <div class="section-header">
                        <span><i class="bx bx-log-in me-2"></i>Contact Logs</span>
                    </div>                    
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Remark</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Contacted By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($lead->contactLogs ?? []) as $contactLog)
                                <tr>
                                    <td>{{ $contactLog->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        <a href="#"
                                            class="editable"
                                            data-name="remark"
                                            data-type="textarea"
                                            data-pk="{{ $contactLog->id }}"
                                            data-url="{{ route('counselor.lead.contact_logs.update', $contactLog->id) }}"
                                            data-title="Edit Remark">{{ $contactLog->remark }}</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="duration"
                                           data-type="number"
                                           data-pk="{{ $contactLog->id }}"
                                           data-url="{{ route('counselor.lead.contact_logs.update', $contactLog->id) }}"
                                           data-title="Edit Duration">
                                            {{ $contactLog->duration }}
                                        </a>Minutes
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="type"
                                           data-type="select"
                                           data-pk="{{ $contactLog->id }}"
                                           data-url="{{ route('counselor.lead.contact_logs.update', $contactLog->id) }}"
                                           data-title="Edit Type"
                                           data-source='[{"value":"Call","text":"Call"},{"value":"Email","text":"Email"},{"value":"In-Person","text":"In-Person"},{"value":"SMS","text":"SMS"},{"value":"WhatsApp","text":"WhatsApp"},{"value":"Other","text":"Other"}]'
                                           data-value="{{ $contactLog->type }}">
                                            {{ $contactLog->type }}
                                        </a><br>
                                        <a href="#"
                                           class="editable"
                                           data-name="response_type"
                                           data-type="select"
                                           data-pk="{{ $contactLog->id }}"
                                           data-url="{{ route('counselor.lead.contact_logs.update', $contactLog->id) }}"
                                           data-title="Edit Type"
                                           data-source='[{"value":"Positive","text":"Positive"},{"value":"Negative","text":"Negative"},{"value":"Neutral","text":"Neutral"},{"value":"RNR","text":"RNR"},{"value":"Invalid Number","text":"Invalid Number"}]'
                                           data-value="{{ $contactLog->response_type }}">
                                            {{ $contactLog->response_type }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="contacted_by"
                                           data-type="text"
                                           data-pk="{{ $contactLog->id }}"
                                           data-url="{{ route('counselor.lead.contact_logs.update', $contactLog->id) }}"
                                           data-title="Edit Contacted By">
                                            {{ $contactLog->contacted_by }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('counselor.lead.contact_logs.destroy', $contactLog->id) }}"
                                           class="btn btn-icon btn-outline-danger"
                                           data-confirm-delete="Are you sure you want to delete this contact log?"
                                           title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card lead-content-card" id="education-card" style="display: none;">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bx bx-book me-2"></i>Education</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#educationModal">Add Education</button>
                    </div>
                </div>
                <div class="card-body lead-content-body">
                    <div class="section-header">
                        <span><i class="bx bx-book me-2"></i>Education Qualifications</span>
                    </div>                    
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Qualification</th>
                                    <th>Marks/Percentage</th>
                                    <th>Institute</th>
                                    <th>Year</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($lead->education ?? []) as $education)
                                <tr>
                                    <td>
                                        <a href="#" 
                                           class="editable" 
                                           data-name="qualification" 
                                           data-type="text" 
                                           data-pk="{{ $education->id }}" 
                                           data-url="{{ route('counselor.lead.education.update', $education->id) }}" 
                                           data-title="Edit Qualification">{{ $education->qualification }}</a>
                                    </td>
                                    <td>
                                        <a href="#" 
                                           class="editable" 
                                           data-name="marks" 
                                           data-type="text" 
                                           data-pk="{{ $education->id }}" 
                                           data-url="{{ route('counselor.lead.education.update', $education->id) }}" 
                                           data-title="Edit Marks/Percentage">{{ $education->marks }}</a>
                                    </td>
                                    <td>
                                        <a href="#" 
                                           class="editable" 
                                           data-name="institute" 
                                           data-type="text" 
                                           data-pk="{{ $education->id }}" 
                                           data-url="{{ route('counselor.lead.education.update', $education->id) }}" 
                                           data-title="Edit Institute">{{ $education->institute }}</a>
                                    </td>
                                    <td>
                                        <a href="#" 
                                           class="editable" 
                                           data-name="year" 
                                           data-type="number" 
                                           data-pk="{{ $education->id }}" 
                                           data-url="{{ route('counselor.lead.education.update', $education->id) }}" 
                                           data-title="Edit Year">{{ $education->year }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('counselor.lead.education.destroy', $education->id) }}"
                                           class="btn btn-icon btn-outline-danger"
                                           data-confirm-delete="Are you sure you want to delete this education record?"
                                           title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card lead-content-card" id="exams-card" style="display: none;">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bx bx-test-tube me-2"></i>Exams</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#examModal">Add Exam</button>
                    </div>
                </div>
                <div class="card-body lead-content-body">
                    <div class="section-header">
                        <span><i class="bx bx-test-tube me-2"></i>Competitive Exams</span>
                    </div>                    
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Score</th>
                                    <th>Year</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($lead->exams ?? []) as $exam)
                                <tr>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="exam_name"
                                           data-type="text"
                                           data-pk="{{ $exam->id }}"
                                           data-url="{{ route('counselor.lead.exams.update', $exam->id) }}"
                                           data-title="Edit Exam Name">{{ $exam->exam_name }}</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="score"
                                           data-type="number"
                                           data-pk="{{ $exam->id }}"
                                           data-url="{{ route('counselor.lead.exams.update', $exam->id) }}"
                                           data-title="Edit Score">{{ $exam->score }}</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="year"
                                           data-type="number"
                                           data-pk="{{ $exam->id }}"
                                           data-url="{{ route('counselor.lead.exams.update', $exam->id) }}"
                                           data-title="Edit Year">{{ $exam->year }}</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="editable"
                                           data-name="remarks"
                                           data-type="textarea"
                                           data-pk="{{ $exam->id }}"
                                           data-url="{{ route('counselor.lead.exams.update', $exam->id) }}"
                                           data-title="Edit Remarks">{{ $exam->remarks }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('counselor.lead.exams.destroy', $exam->id) }}"
                                           class="btn btn-icon btn-outline-danger"
                                           data-confirm-delete="Are you sure you want to delete this exam record?"
                                           title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($lead->student)
            @php
                $feeStudent = $lead->student;
                $feeSummary = app(\App\Services\StudentFeeService::class)->feeSummary($feeStudent);
            @endphp
            <div class="card mt-3 student-fees-panel" id="student-fees-card">
                <div class="card-header border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="mb-0">Student Portal Fees</h5>
                            <small class="text-muted">View-only — fees are set by the Accounts team for {{ $feeStudent->name }}</small>
                        </div>
                        <a href="{{ route('counselor.student-fee-payments.index', ['q' => $feeStudent->lead_ref]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-list-ul me-1"></i>View Payments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="student-fees-summary mb-4" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                        <div class="student-fees-summary__item">
                            <span class="student-fees-summary__label">Registration Fee</span>
                            <span class="student-fees-summary__value">₹{{ number_format($feeSummary['registration_fee'], 2) }}</span>
                            <span class="student-fees-summary__meta">{{ $feeSummary['registration_plan']['label'] ?? 'Not set' }}</span>
                            <span class="student-fees-summary__remain {{ $feeSummary['registration_remaining'] > 0 ? 'is-due' : 'is-done' }}">
                                Remaining ₹{{ number_format($feeSummary['registration_remaining'], 2) }}
                            </span>
                        </div>
                        <div class="student-fees-summary__item">
                            <span class="student-fees-summary__label">Admission Fee</span>
                            <span class="student-fees-summary__value">₹{{ number_format($feeSummary['counselor_fee'], 2) }}</span>
                            <span class="student-fees-summary__meta">Paid ₹{{ number_format($feeSummary['counselor_paid'], 2) }}</span>
                            <span class="student-fees-summary__remain {{ $feeSummary['counselor_remaining'] > 0 ? 'is-due' : 'is-done' }}">
                                Remaining ₹{{ number_format($feeSummary['counselor_remaining'], 2) }}
                            </span>
                        </div>
                        <div class="student-fees-summary__item">
                            <span class="student-fees-summary__label">College Fee</span>
                            <span class="student-fees-summary__value">₹{{ number_format($feeSummary['college_fee'], 2) }}</span>
                            <span class="student-fees-summary__meta">Paid ₹{{ number_format($feeSummary['college_paid'], 2) }}</span>
                            <span class="student-fees-summary__remain {{ $feeSummary['college_remaining'] > 0 ? 'is-due' : 'is-done' }}">
                                Remaining ₹{{ number_format($feeSummary['college_remaining'], 2) }}
                            </span>
                        </div>
                        <div class="student-fees-summary__item student-fees-summary__item--total">
                            <span class="student-fees-summary__label">Total Remaining</span>
                            <span class="student-fees-summary__value">₹{{ number_format($feeSummary['total_remaining'], 2) }}</span>
                            @if($feeSummary['total_remaining'] <= 0 && $feeSummary['fees_set'])
                                <span class="badge bg-success mt-2 align-self-start">Settlement Completed</span>
                            @else
                                <span class="student-fees-summary__meta">All fee types</span>
                            @endif
                        </div>
                    </div>

                    @unless($feeSummary['fees_set'])
                        <div class="alert alert-warning mb-3">Accounts team has not set fees for this student yet.</div>
                    @endunless

                    <div class="student-fees-block student-fees-block--reminder">
                        <div class="student-fees-block__title">Send due reminder</div>
                        <form method="POST" action="{{ route('counselor.leads.student-fees.remind', $lead->id) }}" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Fee type</label>
                                <select name="purpose" class="form-control" required>
                                    <option value="registration_fee">Registration Fee</option>
                                    <option value="counselor_fee">Admission Fee</option>
                                    <option value="college_fee">College Fee</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Message <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="message" class="form-control" placeholder="Please pay the due installment" />
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bx bx-envelope me-1"></i>Email Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <div class="card" id="payments-card" style="display: none;">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Received Payments</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Add Payment</button>
                    </div>
                </div>
                <div class="card-body lead-content-body">
                    <div class="section-header">
                        <span><i class="bx bx-dollar-sign me-2"></i>Payment History</span>
                    </div>                      
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Type</th>
                                    <th>Payment Type</th>
                                    <th>Mode</th>
                                    <th>Bank / Cash Account</th>
                                    <th>Remark</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalReceived = 0;
                                    $totalPaid = 0;
                                    $balance = 0;
                                @endphp
                                @foreach(($lead->payments ?? []) as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                    <td>{{ transaction_types($payment->transaction_type) }}</td>
                                    <td>{{ $payment->payment_type }}</td>
                                    <td>{{ $payment->payment_mode }}</td>
                                    <td>{{ $payment->accountTransaction?->ledgerAccount?->name ?? '—' }}</td>
                                    <td>{{ $payment->remark }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @php
                                    if (in_array($payment->transaction_type, [1,2,3])) {
                                        $totalReceived += $payment->amount;
                                    } elseif (in_array($payment->transaction_type, [4,5,6])) {
                                        $totalPaid += $payment->amount;
                                    }
                                @endphp
                                @endforeach
                                @php
                                    $balance = $totalReceived - $totalPaid;
                                @endphp
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="alert alert-success mb-2">
                                <strong>Total Received:</strong> {{ number_format($totalReceived, 2) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-danger mb-2">
                                <strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info mb-2">
                                <strong>Balance:</strong> {{ number_format($balance, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer -->
<div class="modal fade" id="transferModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.transfer') }}">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    <div class="modal-header border-bottom">
        <h5 class="modal-title" id="backDropModalTitle">Transfer Lead</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-12 mb-3">
        <label for="contactDateTwo" class="form-label">Next FL Date & Time</label>
        <input
          type="datetime-local"
          id="contactDateTwo"
          class="form-control"
          placeholder="Select Date & Time"
          onfocus="this.showPicker()"
          name="next_fl_date"
          min="{{ now()->format('Y-m-d\TH:i') }}"
          required
        />
        </div>
        <div class="col-12 mb-3">
          <label for="counselor" class="form-label">Counselor</label>
            <select id="counselor" class="form-select " name="counselor_id" required>
                <option value="">Select Counselor</option>
                @foreach($counselors as $counselor)
                    @if($counselor['value'] != $lead->counselor_id)
                        <option value="{{ $counselor['value'] }}">{{ $counselor['text'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        
        <div class="col-12 mb-3">
          <label for="transfer_note" class="form-label">Transfer Note</label>
          <input
            type="text"
            id="transfer_note"
            name="transfer_note"
            class="form-control"
            placeholder="Enter Transfer Note"
          />
        </div>
        
      </div>
    </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<!-- Education Modal -->
<div class="modal fade" id="educationModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.education.store') }}">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    <div class="modal-header border-bottom">
        <h5 class="modal-title" id="backDropModalTitle">Add Exam</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-12 mb-3">
          <label for="qualification" class="form-label">Qualification</label>
          <input
            type="text"
            id="qualification"
            name="qualification"
            class="form-control"
            placeholder="Enter Qualification"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="marks" class="form-label">Marks/Percentage</label>
          <input
            type="text"
            id="marks"
            name="marks"
            class="form-control"
            placeholder="Enter Marks or Percentage"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="institute" class="form-label">Institute</label>
          <input
            type="text"
            id="institute"
            name="institute"
            class="form-control"
            placeholder="Enter Institute Name"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="year" class="form-label">Year</label>
          <input
            type="number"
            id="year"
            name="year"
            class="form-control"
            placeholder="Enter Year"
            required
          />
        </div>
      </div>
    </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<!-- Exam Modal -->
<div class="modal fade" id="examModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.exams.store') }}">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="backDropModalTitle">Add Exam</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-12 mb-3">
          <label for="examName" class="form-label">Exam Name</label>
          <input
            type="text"
            id="examName"
            class="form-control"
            placeholder="Enter Exam Name"
            name="exam_name"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="examScore" class="form-label">Score</label>
          <input
            type="number"
            id="examScore"
            class="form-control"
            placeholder="Enter Exam Score"
            name="score"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="examYear" class="form-label">Year</label>
          <input
            type="number"
            id="examYear"
            class="form-control"
            placeholder="Enter Exam Year"
            name="year"
            required
          />
        </div>
        <div class="col-12 mb-3">
          <label for="examRemarks" class="form-label">Remarks</label>
          <textarea
            id="examRemarks"
            class="form-control"
            rows="3"
            placeholder="Enter Remarks"
            name="remarks"
          ></textarea>
        </div>
      </div>
    </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<!-- Payments Modal -->
<div class="modal fade" id="paymentModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.payments.store') }}">
      @csrf
      <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="backDropModalTitle">Add Payment</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <div class="row">
          @include('partials.lead-payment-form-fields', [
              'ledgerAccounts' => $ledgerAccounts ?? collect(),
          ])
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<!-- Followup Modal -->
<div class="modal fade" id="followupModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.contact_logs.store') }}">
      @csrf
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="backDropModalTitle">Add Contact Log</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
    <div class="modal-body">
      <div class="row">
        
        <div class="col-12 mb-3">
        <label for="contactRemark" class="form-label">Remark</label>
        <textarea
          id="contactRemark"
          class="form-control"
          rows="3"
          placeholder="Enter Remark"
          name="remark"
          required
        ></textarea>
        </div>
        <div class="col-6 mb-3">
        <label for="contactDuration" class="form-label">Duration (mins)</label>
        <input
          type="number"
          id="contactDuration"
          class="form-control"
          placeholder="Enter Duration"
          name="duration"
          required
        />
        </div>
        <div class="col-6 mb-3">
        <label for="contactType" class="form-label">Type</label>
        <select id="contactType" class="form-select" name="type" required>
          <option value="Call">Call</option>
          <option value="Email">Email</option>
          <option value="In-Person">In-Person</option>
          <option value="SMS">SMS</option>
          <option value="WhatsApp">WhatsApp</option>
          <option value="Other">Other</option>
        </select>
        </div>
        <div class="col-6 mb-3">
        <label for="responseType" class="form-label">Response Type</label>
        <select id="responseType" class="form-select" name="response_type" required>
          <option value="Positive">Positive</option>
          <option value="Negative">Negative</option>
          <option value="Neutral">Neutral</option>
          <option value="RNR">RNR</option>
          <option value="Invalid Number">Invalid Number</option>
        </select>
        </div>
        <div class="col-6 mb-3">
        <label for="contactedBy" class="form-label">Contacted By</label>
        <input
          type="text"
          id="contactedBy"
          class="form-control"
          placeholder="Enter Name of Contact Person"
          name="contacted_by"
          required
        />
        </div>
        <div class="col-12 mb-3">
            <label for="contactStatus" class="form-label">Status</label>
            <select id="contactStatus" class="form-select" name="status" required>
                <option value="">Select Status</option>
                @foreach($statuses as $status)
                    @if(!in_array($status, ['Admission', 'Application', 'Reservation', 'Cancelled']))
                        <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
        <label for="contactDate" class="form-label">Next FL Date & Time</label>
        <input
          type="datetime-local"
          id="contactDate"
          class="form-control"
          placeholder="Select Date & Time"
          onfocus="this.showPicker()"
          name="contact_date"
          min="{{ now()->format('Y-m-d\TH:i') }}"
          required
        />
        </div>
      </div>
    </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>


<!-- Admission Modal -->
<div class="modal fade" id="admissionModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.admission.store') }}">
      @csrf
      <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Process Admission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-6 mb-3">
            <label for="admissionDate" class="form-label">Admission Date</label>
            <input
              type="date"
              id="admissionDate"
              name="admission_date"
              class="form-control"
              value="{{ old('admission_date', $lead->admission_date ? \Carbon\Carbon::parse($lead->admission_date)->format('Y-m-d') : date('Y-m-d')) }}"
              onfocus="this.showPicker()"
              required
            />
          </div>
          
          <div class="col-6 mb-3">
            <label for="admissionNumber" class="form-label">Admission Number</label>
            <input
              type="text"
              id="admissionNumber"
              name="admission_no"
              class="form-control"
              placeholder="Enter Admission Number"
              value="{{ old('admission_no', $lead->admission_no ?? '') }}"
              required
            />
          </div>

          <div class="col-6 mb-3">
            <label for="admissionCollege" class="form-label">College</label>
            <select id="admissionCollege" class="form-select" name="college_id" required>
              <option value="">Select College</option>
              @foreach($colleges as $college)
            <option value="{{ $college['value'] }}" 
              {{ old('college_id', $lead->college_id ?? '') == $college['value'] ? 'selected' : '' }}>
              {{ $college['text'] }}
            </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 mb-3">
            <label for="admissionCourse" class="form-label">Course</label>
            <select id="admissionCourse" class="form-select" name="course_id" required>
              <option value="">Select Course</option>
              @foreach($courses as $course)
            <option value="{{ $course['value'] }}" 
              {{ old('course_id', $lead->course_id ?? '') == $course['value'] ? 'selected' : '' }}>
              {{ $course['text'] }}
            </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 mb-3">
            <label for="commission" class="form-label">Commission Amount</label>
            <input
              type="number"
              id="commission"
              name="commission"
              class="form-control"
              placeholder="Enter Commission Amount"
              value="{{ old('commission', $lead->commission ?? '') }}"
            />
          </div>
          <div class="col-6 mb-3">
            <label for="admissionCourse" class="form-label">Agent</label>
            <select id="admissionAgent" class="form-select" name="agent_id">
              <option value="">Select Agent</option>
              @foreach($agents as $agent)
            <option value="{{ $agent['value'] }}" 
              {{ old('agent_id', $lead->agent_id ?? '') == $agent['value'] ? 'selected' : '' }}>
              {{ $agent['text'] }}
            </option>
              @endforeach
            </select>
          </div>
          <div class="col-6 mb-3">
            <label for="agentCommission" class="form-label">Agent Commission</label>
            <input
              type="number"
              id="agentCommission"
              name="agent_commission"
              class="form-control"
              placeholder="Enter Agent Commission"
              value="{{ old('agent_commission', $lead->agent_commission ?? '') }}"
            />
          </div>

          <div class="col-12 mb-3">
            <label for="termsConditions" class="form-label">Terms & Conditions</label>
            <textarea
              id="termsConditions"
              name="terms_and_conditions"
              class="form-control"
              rows="3"
              placeholder="Enter Terms & Conditions"
            >{{ old('terms_and_conditions', $lead->terms_and_conditions ?? '') }}</textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Process Admission</button>
      </div>
    </form>
  </div>
</div>

<!-- Application Modal -->
<div class="modal fade" id="applicationModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.application.store') }}">
      @csrf
      <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Process Application</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-12 mb-3">
            <label for="applicationDate" class="form-label">Application Date</label>
            <input
              type="date"
              id="applicationDate"
              name="application_date"
              class="form-control"
              value="{{ date('Y-m-d') }}"
              onfocus="this.showPicker()"
              required
            />
          </div>
          <div class="col-6 mb-3">
            <label for="admissionCollege" class="form-label">College</label>
            <select id="admissionCollege" class="form-select" name="college_id" required>
              <option value="">Select College</option>
              @foreach($colleges as $college)
            <option value="{{ $college['value'] }}" 
              {{ old('college_id', $lead->college_id ?? '') == $college['value'] ? 'selected' : '' }}>
              {{ $college['text'] }}
            </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 mb-3">
            <label for="admissionCourse" class="form-label">Course</label>
            <select id="admissionCourse" class="form-select" name="course_id" required>
              <option value="">Select Course</option>
              @foreach($courses as $course)
            <option value="{{ $course['value'] }}" 
              {{ old('course_id', $lead->course_id ?? '') == $course['value'] ? 'selected' : '' }}>
              {{ $course['text'] }}
            </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 mb-3">
            <label for="applicationNote" class="form-label">Application Note</label>
            <textarea
              id="applicationNote"
              name="application_note"
              class="form-control"
              rows="3"
              placeholder="Enter Application Notes"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Process Application</button>
      </div>
    </form>
  </div>
</div>


<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.reservation.store') }}">
      @csrf
      <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Process Reservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-12 mb-3">
            <label for="reservationDate" class="form-label">Reservation Date</label>
            <input
              type="date"
              id="reservationDate"
              name="reservation_date"
              class="form-control"
              value="{{ date('Y-m-d') }}"
              required
              onfocus="this.showPicker()"
            />
          </div>

          <div class="col-6 mb-3">
            <label for="reservationCollege" class="form-label">College</label>
            <select id="reservationCollege" class="form-select" name="college_id" required>
              <option value="">Select College</option>
              @foreach($colleges as $college)
                <option value="{{ $college['value'] }}" 
                  {{ old('college_id', $lead->college_id ?? '') == $college['value'] ? 'selected' : '' }}>
                  {{ $college['text'] }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 mb-3">
            <label for="reservationCourse" class="form-label">Course</label>
            <select id="reservationCourse" class="form-select" name="course_id" required>
              <option value="">Select Course</option>
              @foreach($courses as $course)
                <option value="{{ $course['value'] }}" 
                  {{ old('course_id', $lead->course_id ?? '') == $course['value'] ? 'selected' : '' }}>
                  {{ $course['text'] }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 mb-3">
            <label for="reservationNote" class="form-label">Note</label>
            <textarea
              id="reservationNote"
              name="reservation_note"
              class="form-control"
              rows="3"
              placeholder="Enter Reservation Note"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Process Reservation</button>
      </div>
    </form>
  </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('counselor.lead.cancel') }}">
      @csrf
      <input type="hidden" name="lead_id" value="{{ $lead->id }}">
      
      <div class="modal-header border-bottom">
        <h5 class="modal-title">Cancel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-6 mb-3">
            <label for="cancelDate" class="form-label">Cancellation Date</label>
            <input
              type="date"
              id="cancelDate"
              name="cancel_date"
              class="form-control"
              value="{{ date('Y-m-d') }}"
              required
              onfocus="this.showPicker()"
            />
          </div>

          <div class="col-6 mb-3">
            <label for="cancelReason" class="form-label">Cancellation Reason</label>
            <select id="cancelReason" class="form-select" name="cancel_reason" required>
              <option value="">Select Reason</option>
              <option value="Change of Mind">Change of Mind</option>
              <option value="Financial Issues">Financial Issues</option>
              <option value="Got Better Option">Got Better Option</option>
              <option value="Personal Reason">Personal Reason</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="col-12 mb-3">
            <label for="cancelNote" class="form-label">Cancellation Note</label>
            <textarea
              id="cancelNote"
              name="cancel_note"
              class="form-control"
              rows="3"
              placeholder="Enter Cancellation Note"
              required
            ></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Cancel Lead</button>
      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script src="{{url('/crm/assets/js/ui-toasts.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap toasts
    function initializeToasts() {
        const toastElements = document.querySelectorAll('.bs-toast');
        toastElements.forEach(function(element) {
            if (!element.classList.contains('initialized')) {
                const toast = new bootstrap.Toast(element, {
                    autohide: true,
                    delay: 2000
                });
                toast.show();
                element.classList.add('initialized');
            }
        });
    }

    // Initialize toasts on page load
    initializeToasts();

    // Function to show dynamic toasts
    window.showToast = function(message, type = 'success') {
        // Remove existing toasts
        document.querySelectorAll('.bs-toast').forEach(el => el.remove());

        // Create new toast
        const toastHtml = `
            <div class="bs-toast toast bg-${type}" 
                 role="alert" 
                 aria-live="assertive" 
                 aria-atomic="true" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 1080;">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-semibold">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                    <small>Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', toastHtml);
        initializeToasts();
    };

    // Handle editable success/error
    $('.editable').on('save', function(e, params) {
        if (params.response && params.response.success) {
            showToast('Updated successfully', 'success');
        } else {
            showToast('Update failed', 'danger');
        }
    });

    $('.editable').on('error', function() {
        showToast('Update failed', 'danger');
    });
});
</script>
<script>
    $(function() {
    $('.select2').select2({
        width: '100%',
        dropdownParent: $(document.body) // Ensure dropdown is appended to body
    });
    // Initialize with default options
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.params = function (params) {
        params._token = '{{ csrf_token() }}';
        return params;
    };
    $('.editable').editable({
        // url: '{{ route("counselor.leads.update", $lead->id) }}',
        ajaxOptions: {
            type: 'POST',
            dataType: 'json'
        },
        success: function(response, newValue) {
            if (!response) return;
            if (response.success) {
                console.log('Updated successfully:', response);
            } else {
                console.error('Server error:', response.error);
                return response.error; // Will show as validation error
            }
        },
        error: function(response) {
            console.error('AJAX error:', response);
            return 'Server communication failed';
        }
    });

    // Enable select2 for editable fields with data-type="select2"
    $.fn.editable.types.select2 = $.extend({}, $.fn.editabletypes.select, {
        render: function() {
            $.fn.editabletypes.select2.superclass.render.call(this);
            var self = this;
            this.$input.select2({
                width: 'resolve',
                dropdownParent: $(document.body)
            });
        },
        value2input: function(value) {
            this.$input.val(value).trigger('change');
        }
    });
});
    // $(document).ready(function() {
    //     $('.editable').editable({
    //         mode: 'inline',
    //         success: function(response, newValue) {
    //             // Handle success response
    //             console.log('Updated successfully:', response);
    //         },
    //         error: function(response) {
    //             // Handle error response
    //             console.error('Error updating:', response);
    //         }
    //     });
    // });
</script>
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash.substring(1);
        if (hash) {
            // Activate the corresponding menu and show the content
            const activeLink = document.querySelector(`.lead-profile-nav .nav-link[href="#${hash}"]`);
            if (activeLink) {
                activateMenu(activeLink);
                showContent(hash);
            }
        }
    });

    function activateMenu(element) {
        // Remove 'active' class from all nav links
        document.querySelectorAll('.lead-profile-nav .nav-link').forEach(link => link.classList.remove('active'));
        // Add 'active' class to the clicked link
        element.classList.add('active');
    }

    function showContent(section) {
        // Hide all target cards except the menu card
        document.querySelectorAll('.lead-profile-content-col .card').forEach(card => card.style.display = 'none');
        // Show the selected card
        const selectedCard = document.getElementById(`${section}-card`);
        if (selectedCard) {
            selectedCard.style.display = 'block';
        }
        // Update the URL hash
        history.pushState(null, null, `#${section}`);
    }
</script> 
<script>
    function activateMenu(element) {
        // Remove 'active' class from all nav links
        document.querySelectorAll('.lead-profile-nav .nav-link').forEach(link => link.classList.remove('active'));
        // Add 'active' class to the clicked link
        element.classList.add('active');
    }

    function showContent(section) {
        // Hide all target cards except the menu card
        document.querySelectorAll('.lead-profile-content-col .card').forEach(card => card.style.display = 'none');
        // Show the selected card
        const selectedCard = document.getElementById(`${section}-card`);
        if (selectedCard) {
            selectedCard.style.display = 'block';
        }
    }
$(function() {
    $.fn.editable.defaults.params = function (params) {
        params._token = '{{ csrf_token() }}';
        return params;
    };

    $('.editable').editable({
        url: '{{ route("counselor.leads.update", $lead->id) }}',
        mode: 'inline',
        ajaxOptions: {
            type: 'POST',
            dataType: 'json'
        },
        success: function(response, newValue) {
            if (response.success) {
                toastr.success('Updated successfully');
            }
        },
        error: function(response) {
            toastr.error('Update failed');
        }
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.querySelector('.toast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 3000, // Auto-hide after 3 seconds
                autohide: true
            });
            // toast.show();
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get holidays from PHP
    const holidays = @json($holidays);

    // Function to check if date is holiday
    function isHoliday(date) {
        return holidays.some(holiday => holiday.date === date);
    }

    // Function to get holiday name
    function getHolidayName(date) {
        const holiday = holidays.find(h => h.date === date);
        return holiday ? holiday.name : '';
    }

    // Function to check if date is Sunday
    function isSunday(date) {
        date: 'YYYY-MM-DD'
        const d = new Date(date);
        return d.getDay() === 0;
    }

    // Initialize datepicker for all contact date inputs
    ['contactDate', 'contactDateTwo'].forEach(function(inputId) {
        const dateInput = document.getElementById(inputId);
        if (dateInput) {
            // Set minimum date and time
            const now = new Date();
            now.setMinutes(now.getMinutes() + 1); // Add 1 minute to current time
            dateInput.min = now.toISOString().slice(0, 16);

            // Handle date selection
            dateInput.addEventListener('input', function(e) {
                const selectedDate = this.value.split('T')[0];

                if (isHoliday(selectedDate)) {
                    const holidayName = getHolidayName(selectedDate);
                    alert(`Selected date (${selectedDate}) is a holiday: ${holidayName}\nPlease choose another date.`);
                    this.value = ''; // Clear the input
                } else if (isSunday(selectedDate)) {
                    alert(`Selected date (${selectedDate}) is a Sunday. Please choose another date.`);
                    this.value = '';
                }
            });

            // Custom validation
            dateInput.addEventListener('invalid', function(e) {
                if (this.value) {
                    const selectedDate = this.value.split('T')[0];
                    if (isHoliday(selectedDate)) {
                        e.preventDefault();
                        this.setCustomValidity(`Selected date is a holiday: ${getHolidayName(selectedDate)}`);
                    } else if (isSunday(selectedDate)) {
                        e.preventDefault();
                        this.setCustomValidity('Selected date is a Sunday.');
                    }
                }
            });

            // Reset validation message
            dateInput.addEventListener('input', function() {
                this.setCustomValidity('');
            });
        }
    });

    // Add warning text under date inputs
    ['contactDate', 'contactDateTwo'].forEach(function(inputId) {
        const dateInput = document.getElementById(inputId);
        if (dateInput) {
            const warningDiv = document.createElement('small');
            warningDiv.className = 'text-muted d-block mt-1';
            warningDiv.innerHTML = `Next ${holidays.length} holidays: ` +
                holidays.slice(0, 3).map(h =>
                    `<span class="text-danger">${h.date} (${h.name})</span>`
                ).join(', ');
                // +`<br><span class="text-danger">Sundays are disabled.</span>`
            dateInput.parentNode.appendChild(warningDiv);
        }
    });
});
</script>
<script>
function updatePhoto(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData($('#photoForm')[0]);
        
        // Show loading state
        $('#leadPhoto').css('opacity', '0.5');

        $.ajax({
            url: '{{ route("counselor.leads.update.photo", $lead->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#leadPhoto').attr('src', response.photo).removeClass('d-none');
                    $('#leadPhotoPlaceholder').addClass('d-none');
                    toastr.success('Photo updated successfully');
                } else {
                    toastr.error('Failed to update photo');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                toastr.error('An error occurred while updating photo');
            },
            complete: function() {
                // Remove loading state
                $('#leadPhoto').css('opacity', '1');
            }
        });
    }
}
</script>
@endsection