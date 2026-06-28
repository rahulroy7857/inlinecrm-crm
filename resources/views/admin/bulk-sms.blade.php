@extends('admin.layouts.app')
@section('title', 'Bulk SMS')
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
                        <h5 class="">Bulk SMS</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" name="from_date" placeholder="From Date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate" name="to_date" placeholder="To Date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">College</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select college</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">Course</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select course</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">Source</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select source</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">Status</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select state</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">Counselor</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select counselor</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="referenceName" class="form-label">State</label>
                                    <select class="form-select" id="referenceName" name="column_name">
                                        <option value="" disabled selected>Select state</option>
                                        <option value="Ref1">Mobile</option>
                                        <option value="Ref2">Email</option>
                                        <option value="Ref3">State</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="referenceName" class="form-label">Content</label>
                                    <textarea class="form-control" id="referenceName" name="column_name" rows="4" placeholder="Enter your message here..."></textarea>
                                </div>
                                <div class="col-md-12 mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Send SMS</button>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div>

                <div class="card-body mt-3 border-top">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>College</th>
                                    <th>Course</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Counselor</th>
                                    <th>State</th>
                                    <th>SMS Content</th>
                                    <th>No. of Leads</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>1</td>
                                    <td>LEAD001</td>
                                    <td>ABC College</td>
                                    <td>Engineering</td>
                                    <td>Website</td>
                                    <td>New</td>
                                    <td>John Counselor</td>
                                    <td>California</td>
                                    <td>Welcome to ABC College! Explore our Engineering programs.</td>
                                    <td>50</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>LEAD002</td>
                                    <td>XYZ University</td>
                                    <td>Medical</td>
                                    <td>Referral</td>
                                    <td>Contacted</td>
                                    <td>Jane Counselor</td>
                                    <td>Texas</td>
                                    <td>Join XYZ University for a bright future in Medical studies.</td>
                                    <td>30</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>LEAD003</td>
                                    <td>LMN Institute</td>
                                    <td>Business</td>
                                    <td>Social Media</td>
                                    <td>New</td>
                                    <td>Michael Counselor</td>
                                    <td>Florida</td>
                                    <td>Discover Business opportunities at LMN Institute.</td>
                                    <td>40</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>LEAD004</td>
                                    <td>PQR College</td>
                                    <td>Arts</td>
                                    <td>Email Campaign</td>
                                    <td>Contacted</td>
                                    <td>Emily Counselor</td>
                                    <td>New York</td>
                                    <td>Explore your creativity with Arts programs at PQR College.</td>
                                    <td>25</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   
@include('admin.partials.datatables-scripts')
<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
    });
</script>
@endsection