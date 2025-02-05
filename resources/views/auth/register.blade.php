{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>API Server</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    @stack('third_party_stylesheets')

    @stack('page_css')
</head>

<body class="with-welcome-text">
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="assets/images/logo.svg" alt="logo">
                            </div>
                            <h4>New here?</h4>
                            <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6>
                            <form class="pt-3" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <input id="name" type="text"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name"
                                        autofocus placeholder="Enter your name">
                                    @error('name')
                                        <label id="cname-error" class="error mt-2 text-danger" for="cname"
                                            role="alert">
                                            {{ $message }}
                                        </label>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus placeholder="Enter your email" placeholder="Enter your email">
                                    @error('email')
                                        <label id="cname-error" class="error mt-2 text-danger" for="cname" role="alert">
                                            {{ $message }}
                                        </label>
                                    @enderror
                                </div>
                                {{-- <div class="form-group">
                                    <select class="form-control form-control-lg" id="exampleFormControlSelect2">
                                        <option>Country</option>
                                        <option>United States of America</option>
                                        <option>United Kingdom</option>
                                        <option>India</option>
                                        <option>Germany</option>
                                        <option>Argentina</option>
                                    </select>
                                </div> --}}
                                <div class="form-group">
                                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your password">
                                    @error('password')
                                        <label id="cname-error" class="error mt-2 text-danger" for="cname" role="alert">
                                            {{ $message }}
                                        </label>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="Password confirm">
                                    @error('password-confirm')
                                        <label id="cname-error" class="error mt-2 text-danger" for="cname" role="alert">
                                            {{ $message }}
                                        </label>
                                    @enderror
                                </div>
                                {{-- <div class="mb-4">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input">
                                            I agree to all Terms & Conditions
                                        </label>
                                    </div>
                                </div> --}}
                                <div class="mt-3">
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                    type="submit" >SIGN UP</button>
                                </div>
                                <div class="text-center mt-4 fw-light">
                                    Already have an account? <a href="/login" class="text-primary">Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/Chart.roundedBarCharts.js"></script>
    <!-- End custom js for this page-->
    @stack('third_party_scripts')

    @stack('page_scripts')
</body>

</html>
