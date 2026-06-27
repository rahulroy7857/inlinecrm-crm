@extends('admin.layouts.app')
@section('title', 'General')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">General</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="" method="POST" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="appName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="appName" name="app_name" placeholder="Enter application name">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="appDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="appDescription" name="app_description" rows="4" placeholder="Enter application description"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="appAddress" name="app_address" placeholder="Enter address">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appPlace" class="form-label">Place</label>
                                    <input type="text" class="form-control" id="appPlace" name="app_place" placeholder="Enter place">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appDistrict" class="form-label">District</label>
                                    <input type="text" class="form-control" id="appDistrict" name="app_district" placeholder="Enter district">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="appState" name="app_state" placeholder="Enter state">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appCountry" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="appCountry" name="app_country" placeholder="Enter country">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appPin" class="form-label">Pin Code</label>
                                    <input type="text" class="form-control" id="appPin" name="app_pin" placeholder="Enter pin code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appPin" class="form-label">GST NO</label>
                                    <input type="text" class="form-control" id="appGST" name="app_gst" placeholder="Enter GST number">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appLogo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="appLogo" name="app_logo">
                                </div>
                                <div class="col-md-12 mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Update</button>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   

@endsection