@extends('admin.layouts.app')
@section('title', 'Payment Gateway')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Payment Gateway</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="" method="POST" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gatewayName" class="form-label">Gateway Name</label>
                                    <select class="form-control" id="gatewayName" name="gateway_name">
                                        <option value="paypal">PayPal</option>
                                        <option value="stripe">Stripe</option>
                                        <option value="square">Square</option>
                                        <option value="razorepay">RazorePay</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="merchantId" class="form-label">Merchant ID</label>
                                    <input type="text" class="form-control" id="merchantId" name="merchant_id" placeholder="Enter merchant ID">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="merchantKey" class="form-label">Merchant Key</label>
                                    <input type="text" class="form-control" id="merchantKey" name="merchant_key" placeholder="Enter merchant key">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apiUrl" class="form-label">API URL</label>
                                    <input type="text" class="form-control" id="apiUrl" name="api_url" placeholder="Enter API URL">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="callbackUrl" class="form-label">Callback URL</label>
                                    <input type="text" class="form-control" id="callbackUrl" name="callback_url" placeholder="Enter callback URL">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Save Settings</button>
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