@extends('counselor.layouts.app')
@section('title', "Today's Tasks")
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Today's Tasks</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Next Follow Up</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($leads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->mobile }}</td>
                                    <td>{{ $lead->course->name ?? '-' }}</td>
                                    <td>{{ $lead->source->name ?? '-' }}</td>
                                    <td>{!! \App\Helpers\LeadStatus::getBadge($lead->status) !!}</td>
                                    <td>{{ $lead->next_follow_up->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('counselor.leads.show', $lead->id) }}" class="btn btn-icon btn-outline-primary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        
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

<div class="modal fade" id="followupModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Follow-up Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="reminderMessage"></p>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')   
@include('admin.partials.datatables-scripts')
@php
    $followupReminders = $leads->map(function ($lead) {
        return [
            'id' => $lead->id,
            'lead_id' => $lead->lead_id,
            'name' => $lead->name,
            'status' => $lead->status,
            'due_at' => $lead->next_follow_up->timestamp * 1000,
            'due_label' => $lead->next_follow_up->format('d M Y h:i A'),
        ];
    })->values();
@endphp
<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
    });

    document.addEventListener('DOMContentLoaded', function () {
        const followups = @json($followupReminders);
        const modalElement = document.getElementById('followupModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const fiveMinutes = 5 * 60 * 1000;

        function checkFollowupReminders() {
            if (modalElement.classList.contains('show')) {
                return;
            }

            const now = Date.now();
            const followup = followups.find(function (item) {
                const timeUntilDue = item.due_at - now;
                const reminderKey = `counselor-followup-reminder-${item.id}-${item.due_at}`;

                return timeUntilDue >= 0
                    && timeUntilDue <= fiveMinutes
                    && !sessionStorage.getItem(reminderKey);
            });

            if (!followup) {
                return;
            }

            const reminderKey = `counselor-followup-reminder-${followup.id}-${followup.due_at}`;
            sessionStorage.setItem(reminderKey, 'shown');

            document.getElementById('reminderMessage').textContent =
                `Follow-up for ${followup.name} (${followup.lead_id}) is due at ${followup.due_label}. Please follow up now.`;

            modal.show();
        }

        checkFollowupReminders();
        setInterval(checkFollowupReminders, 30000);
    });
</script>
@endsection
