<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Star Admin2') - {{ config('app.name') }}</title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/feather/feather.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/ti-icons/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/typicons/typicons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page (bisa dipindah ke @stack('styles') tiap halaman kalau tidak semua halaman butuh) -->
    @stack('styles')

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.png') }}" />
  </head>
  <body class="with-welcome-text">
    <div class="container-scroller">

      <div class="row p-0 m-0 proBanner" id="proBanner">
        <div class="col-md-12 p-0 m-0">
          <div class="card-body card-body-padding px-3 d-flex align-items-center justify-content-between">
            <div class="ps-lg-3">
              <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0 fw-medium me-3 buy-now-text">Free 24/7 customer support, updates, and more with this template!</p>
                <a href="https://www.bootstrapdash.com/product/star-admin-pro/" target="_blank" class="btn me-2 buy-now-btn border-0">Buy Now</a>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <a href="https://www.bootstrapdash.com/product/star-admin-pro/"><i class="ti-home me-3 text-white"></i></a>
              <button id="bannerClose" class="btn border-0 p-0">
                <i class="ti-close text-white"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      @include('layouts.partials.admin-navbar')

      <div class="container-fluid page-body-wrapper">

        @include('layouts.partials.admin-sidebar')

        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>
          <!-- content-wrapper ends -->

          @include('layouts.partials.admin-footer')

        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('assets/admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- endinject -->

    <!-- inject:js -->
    <script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/admin/js/template.js') }}"></script>
    <script src="{{ asset('assets/admin/js/settings.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/admin/js/todolist.js') }}"></script>
    <!-- endinject -->

    <!-- Custom js -->
    <script src="{{ asset('assets/admin/js/jquery.cookie.js') }}" type="text/javascript"></script>

    @stack('scripts')
  </body>
</html>
