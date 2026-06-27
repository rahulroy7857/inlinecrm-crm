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
            <div class="authentication-inner" style="max-width: 800px;">
                <!-- Application Form -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center border-bottom mb-3 pb-3">
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
                                <span class="app-brand-text demo text-body fw-bolder">CRM</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <!-- <h4 class="mb-2">Welcome to EduConsult! 👋</h4> -->
                        <!-- <p class="mb-4">Fill out the form below to apply for our consultancy services.</p> -->

                        <form id="applicationForm" class="mb-3" method="POST" action="">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="fullName"
                                        name="fullName"
                                        value="{{ old('fullName') }}"
                                        placeholder="Enter your full name"
                                        required
                                    />
                                    @error('fullName')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        required
                                    />
                                    @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="Enter your phone number"
                                        required
                                    />
                                    @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="dob"
                                        name="dob"
                                        value="{{ old('dob') }}"
                                        required
                                    />
                                    @error('dob')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Full Address</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="address"
                                    name="address"
                                    value="{{ old('address') }}"
                                    placeholder="Enter your full address"
                                    required
                                />
                                @error('address')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="place" class="form-label">Place</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="place"
                                        name="place"
                                        value="{{ old('place') }}"
                                        placeholder="Enter your place"
                                        required
                                    />
                                    @error('place')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="state"
                                        name="state"
                                        value="{{ old('state') }}"
                                        placeholder="Enter your state"
                                        required
                                    />
                                    @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="country"
                                        name="country"
                                        value="{{ old('country') }}"
                                        placeholder="Enter your country"
                                        required
                                    />
                                    @error('country')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pin" class="form-label">Pin Code</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="pin"
                                        name="pin"
                                        value="{{ old('pin') }}"
                                        placeholder="Enter your pin code"
                                        required
                                    />
                                    @error('pin')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Course Interested In</label>
                                    <select
                                        class="form-control"
                                        id="course"
                                        name="course"
                                        required
                                    >
                                        <option value="" disabled selected>Select a course</option>
                                        <option value="Engineering" {{ old('course') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                        <option value="Medicine" {{ old('course') == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                                        <option value="Business" {{ old('course') == 'Business' ? 'selected' : '' }}>Business</option>
                                        <option value="Arts" {{ old('course') == 'Arts' ? 'selected' : '' }}>Arts</option>
                                    </select>
                                    @error('course')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="college" class="form-label">College Interested In</label>
                                    <select
                                        class="form-control"
                                        id="college"
                                        name="college"
                                        required
                                    >
                                        <option value="" disabled selected>Select a college</option>
                                        <option value="Harvard" {{ old('college') == 'Harvard' ? 'selected' : '' }}>Harvard</option>
                                        <option value="MIT" {{ old('college') == 'MIT' ? 'selected' : '' }}>MIT</option>
                                        <option value="Stanford" {{ old('college') == 'Stanford' ? 'selected' : '' }}>Stanford</option>
                                        <option value="Oxford" {{ old('college') == 'Oxford' ? 'selected' : '' }}>Oxford</option>
                                    </select>
                                    @error('college')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="educationLevel" class="form-label">Highest Education Level</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="educationLevel"
                                        name="educationLevel"
                                        value="{{ old('educationLevel') }}"
                                        placeholder="e.g., Bachelor's in Engineering"
                                        required
                                    />
                                    @error('educationLevel')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="educationScore" class="form-label">Score/Percentage</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="educationScore"
                                        name="educationScore"
                                        value="{{ old('educationScore') }}"
                                        placeholder="e.g., 85%"
                                        required
                                    />
                                    @error('educationScore')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="examName" class="form-label">Competitive Exam</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="examName"
                                        name="examName"
                                        value="{{ old('examName') }}"
                                        placeholder="e.g., GRE"
                                        required
                                    />
                                    @error('examName')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="examScore" class="form-label">Exam Score</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="examScore"
                                        name="examScore"
                                        value="{{ old('examScore') }}"
                                        placeholder="e.g., 320"
                                        required
                                    />
                                    @error('examScore')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Submit & Pay</button>
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
