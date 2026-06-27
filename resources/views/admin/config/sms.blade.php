@extends('admin.layouts.app')
@section('title', 'SMS')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">SMS</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="" method="POST" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smsGatewayName" class="form-label">SMS Gateway Name</label>
                                    <select class="form-control select2" id="smsGatewayName" name="sms_gateway_name">
                                        <option value="">Select SMS Gateway</option>
                                        <option value="twilio">Twilio</option>
                                        <option value="nexmo">Nexmo</option>
                                        <option value="plivo">Plivo</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smsApiUrl" class="form-label">API URL</label>
                                    <input type="text" class="form-control" id="smsApiUrl" name="sms_api_url" placeholder="Enter API URL">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smsApiKey" class="form-label">API Key</label>
                                    <input type="text" class="form-control" id="smsApiKey" name="sms_api_key" placeholder="Enter API key">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smsSenderId" class="form-label">Sender ID</label>
                                    <input type="text" class="form-control" id="smsSenderId" name="sms_sender_id" placeholder="Enter sender ID">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smsUsername" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="smsUsername" name="sms_username" placeholder="Enter username">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smsPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="smsPassword" name="sms_password" placeholder="Enter password">
                                </div>
                                <div class="col-md-12 mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Save Settings</button>
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