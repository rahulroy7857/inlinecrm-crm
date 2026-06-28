@extends('admin.layouts.app')
@section('title', 'General')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Mail</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="" method="POST" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mailDriver" class="form-label">Mail Driver</label>
                                    <input type="text" class="form-control" id="mailDriver" name="mail_driver" placeholder="Enter mail driver (e.g., smtp)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailHost" class="form-label">Mail Host</label>
                                    <input type="text" class="form-control" id="mailHost" name="mail_host" placeholder="Enter mail host (e.g., smtp.mailtrap.io)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailPort" class="form-label">Mail Port</label>
                                    <input type="text" class="form-control" id="mailPort" name="mail_port" placeholder="Enter mail port (e.g., 587)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailUsername" class="form-label">Mail Username</label>
                                    <input type="text" class="form-control" id="mailUsername" name="mail_username" placeholder="Enter mail username">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailPassword" class="form-label">Mail Password</label>
                                    <input type="password" class="form-control" id="mailPassword" name="mail_password" placeholder="Enter mail password">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailEncryption" class="form-label">Mail Encryption</label>
                                    <input type="text" class="form-control" id="mailEncryption" name="mail_encryption" placeholder="Enter mail encryption (e.g., tls)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailFromAddress" class="form-label">Mail From Address</label>
                                    <input type="email" class="form-control" id="mailFromAddress" name="mail_from_address" placeholder="Enter mail from address">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mailFromName" class="form-label">Mail From Name</label>
                                    <input type="text" class="form-control" id="mailFromName" name="mail_from_name" placeholder="Enter mail from name">
                                </div>
                                <div class="col-md-12 mb-3 d-flex align-items-center mt-3">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" id="sendAdminWelcomeMail" name="send_admin_welcome_mail">
                                        <label class="form-check-label" for="sendAdminWelcomeMail">Send Admin Welcome Mail</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" id="sendCounselorWelcomeMail" name="send_counselor_welcome_mail">
                                        <label class="form-check-label" for="sendCounselorWelcomeMail">Send Counselor Welcome Mail</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="sendReceipt" name="send_receipt">
                                        <label class="form-check-label" for="sendReceipt">Send Receipt</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Update</button>
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