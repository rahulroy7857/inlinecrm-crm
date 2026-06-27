<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Application</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{url('crm/assets/img/favicon/favicon.ico')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{url('crm/assets/vendor/fonts/boxicons.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{url('crm/assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{url('crm/assets/vendor/css/theme-default.css')}}"  />
    <link rel="stylesheet" href="{{url('crm/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{url('crm/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{url('crm/assets/vendor/css/pages/page-auth.css')}}" />
    <!-- Helpers -->
    <script src="{{url('crm/assets/vendor/js/helpers.js')}}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{url('crm/assets/js/config.js')}}"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner" style="max-width: 750px;">
                <!-- Invoice -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <div>
                                <h3 class="fw-bold text-primary mb-1">Invoice</h3>
                                <p class="text-muted">Invoice #12345</p>
                            </div>
                            <div class="text-end">
                                <p class="text-muted mb-1">Date: {{ date('d-m-Y') }}</p>
                                <p class="text-muted">Due Date: {{ date('d-m-Y', strtotime('+7 days')) }}</p>
                            </div>
                        </div>
                        <!-- /Header -->

                        <!-- Invoice Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold text-secondary">Billed From:</h5>
                                <p class="text-dark mb-1">Your Company Name</p>
                                <p class="text-muted mb-1">456 Business Road</p>
                                <p class="text-muted mb-1">City, State, ZIP</p>
                                <p class="text-muted">Email: support@yourcompany.com</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="fw-bold text-secondary">Billed To:</h5>
                                <p class="text-dark mb-1">John Doe</p>
                                <p class="text-muted mb-1">123 Main Street</p>
                                <p class="text-muted mb-1">City, State, ZIP</p>
                                <p class="text-muted">Email: john.doe@example.com</p>
                            </div>
                        </div>
                        <!-- /Invoice Details -->

                        <!-- Fee Breakdown -->
                        <div class="mb-4">
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead class="table-light">
                                        <tr class="table-active border-top">
                                            <th class="text-start" style="width: 80%;">Particular</th>
                                            <th class="text-end" style="width: 20%;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold text-secondary" style="width: 90%;">Application Fee</td>
                                            <td class="text-end text-dark fw-bold" style="width: 10%;">₹1,000</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end text-secondary" style="width: 90%;">SGST (9%)</td>
                                            <td class="text-end text-dark fw-bold" style="width: 10%;">₹90</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end text-secondary" style="width: 90%;">CGST (9%)</td>
                                            <td class="text-end text-dark fw-bold" style="width: 10%;">₹90</td>
                                        </tr>
                                        <tr class="table-active border-top">
                                            <td class="text-end fw-semibold text-secondary" style="width: 90%;">Total Fee</td>
                                            <td class="text-end text-dark fw-bold" style="width: 10%;">₹1,280</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Fee Breakdown -->

                        <!-- Buttons -->
                        <div class="text-center mt-4 d-flex justify-content-center gap-3">
                            <button id="downloadInvoice" class="btn btn-outline-secondary btn-sm shadow-sm">
                                <i class="bx bx-download"></i> Download Invoice
                            </button>
                            <button id="printInvoice" class="btn btn-primary btn-sm shadow-sm">
                                <i class="bx bx-printer"></i> Print Invoice
                            </button>
                        </div>
                        <!-- /Buttons -->
                    </div>
                </div>
                <!-- /Invoice -->
            </div>
        </div>
    </div>

    <script>
        document.getElementById('downloadInvoice').addEventListener('click', function () {
            const element = document.body; // You can target the specific invoice container
            html2pdf().from(element).save('invoice.pdf');
        });

        document.getElementById('printInvoice').addEventListener('click', function () {
            window.print();
        });
    </script>

    <!-- Include html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{url('crm/assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{url('crm/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{url('crm/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{url('crm/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{url('crm/assets/vendor/js/menu.js')}}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{url('crm/assets/js/main.js')}}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
