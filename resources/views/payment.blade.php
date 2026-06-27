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
            <div class="authentication-inner" style="max-width: 650px;">
                <!-- Application Form -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <svg
                                        width="25"
                                        viewBox="0 0 25 42"
                                        version="1.1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                    >
                                        <defs>
                                            <path
                                                d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                                                id="path-1"
                                            ></path>
                                        </defs>
                                        <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                                <g id="Icon" transform="translate(27.000000, 15.000000)">
                                                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                        <mask id="mask-2" fill="white">
                                                            <use xlink:href="#path-1"></use>
                                                        </mask>
                                                        <use fill="#696cff" xlink:href="#path-1"></use>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">EduConsult</span>
                            </a>
                        </div>
                       
                        <form id="paymentForm" class="mb-3 " method="POST" action="">
                            @csrf
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-primary">Payment Summary</h3>
                                <p class="text-muted">Review the breakdown of fees and proceed with the payment.</p>
                            </div>
                            
                            <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <span class="fw-semibold text-secondary text-start">Application Fee</span>
                                <span class="text-dark fw-bold text-end">₹1,000</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <span class="fw-semibold text-secondary text-start">SGST (9%)</span>
                                <span class="text-dark fw-bold text-end">₹90</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <span class="fw-semibold text-secondary text-start">CGST (9%)</span>
                                <span class="text-dark fw-bold text-end">₹90</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <span class="fw-semibold text-secondary text-start">Total Fee</span>
                                <span class="text-success fw-bold text-end">₹1,280</span>
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-primary btn-lg w-100 shadow-sm" type="submit">Proceed to Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Application Form -->
            </div>
        </div>
    </div>

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
