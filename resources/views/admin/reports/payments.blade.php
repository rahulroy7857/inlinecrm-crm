@extends('admin.layouts.app')
@section('title', 'Payments Report')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Payments Report</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('admin.reports.payments') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="toDate" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="type" class="form-label">Payment Type</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">All Types</option>
                                        @foreach($paymentTypes as $value => $label)
                                            <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100" style="margin-top: 32px;">Search</button>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div>

                <div class="card-body mt-3 border-top">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Lead Name</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Mode</th>
                                    <th>Type</th>
                                    <th>College</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="{{ route('admin.leads.show', $payment->lead_id) }}" 
                                            class="text-primary fw-bold"
                                            title="View Lead Profile">
                                                {{ $payment->lead->lead_id ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>{{ $payment->lead->name ?? 'N/A' }}</td>
                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->payment_mode == 'Cash' ? 'success' : 'info' }}">
                                                {{ $payment->payment_mode }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $payment->payment_type ?? 'N/A' }}</div>
                                            <small class="text-muted">
                                                {{ transaction_types($payment->transaction_type) ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>{{ $payment->lead->college->name ?? 'N/A' }}</td>
                                    </tr>
                                    
                                @endforeach
                            </tbody>
                           
                        </table>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   
<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection