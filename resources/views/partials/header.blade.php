<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

    <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto me-xl-0">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <!-- <img src="{{ asset('assets/img/logo.webp') }}" alt=""> -->
      <h1 class="sitename">EasyFolio</h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="{{ url('/#hero') }}" class="active">Home</a></li>
        <li><a href="{{ url('/#about') }}">About</a></li>
        <li><a href="{{ url('/#resume') }}">Resume</a></li>
        <li><a href="{{ url('/#portfolio') }}">Portfolio</a></li>
        <li><a href="{{ url('/#services') }}">Services</a></li>
        <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="#">Dropdown 1</a></li>
            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Deep Dropdown 1</a></li>
                <li><a href="#">Deep Dropdown 2</a></li>
                <li><a href="#">Deep Dropdown 3</a></li>
                <li><a href="#">Deep Dropdown 4</a></li>
                <li><a href="#">Deep Dropdown 5</a></li>
              </ul>
            </li>
            <li><a href="#">Dropdown 2</a></li>
            <li><a href="#">Dropdown 3</a></li>
            <li><a href="#">Dropdown 4</a></li>
          </ul>
        </li>
        <li><a href="{{ url('/#contact') }}">Contact</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <div class="header-social-links">
      <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
      <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
      <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
      <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
    </div>

    <!-- Area Auth / Login yang Sudah Diperbaiki -->
    <div class="header-auth ms-3">
      @guest
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
        @else
          <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Login</a>
        @endif
      @else
        <div class="dropdown">
          <a href="#" class="btn btn-outline-primary btn-sm dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            @if (Route::has('dashboard'))
              <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
            @endif
            <li>
              @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">Logout</button>
                </form>
              @else
                <a class="dropdown-item" href="#">Logout</a>
              @endif
            </li>
          </ul>
        </div>
      @endguest
    </div>

  </div>
</header>
