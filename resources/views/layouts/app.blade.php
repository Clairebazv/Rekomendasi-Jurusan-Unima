<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>SPK PRODI UNIMA</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- Datatables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <!-- Start GA -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- /END GA -->
</head>

<body class="layout-3">
  <div id="app">
    <div class="main-wrapper container">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <a href="index.html" class="navbar-brand sidebar-gone-hide">SPK PRODI UNIMA</a>
        <a href="#" class="nav-link sidebar-gone-show" data-toggle="sidebar"><i class="fas fa-bars"></i></a>
        <ul class="navbar-nav navbar-right ml-auto">

          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
              <div class="d-sm-none d-lg-inline-block">{{Auth::user()->name ?? '-'}}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="{{ route('profil') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profil
              </a>
              <div class="dropdown-divider"></div>

              <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Keluar
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>
        </ul>
      </nav>

      <nav class="navbar navbar-secondary navbar-expand-lg">
        <div class="container">
          <ul class="navbar-nav">
            <li class="nav-item {{ request()->segment(1) == '' ? 'active' : '' }}">
              <a href="{{ route('home') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>
            @if(Auth::user()->role == 'admin')
            <li class="nav-item {{ request()->segment(1) == 'mahasiswa' ? 'active' : '' }}">
              <a href="{{ route('mahasiswa.index') }}" class="nav-link"><i class="fas fa-users"></i><span>Mahasiswa</span></a>
            </li>
            <li class="nav-item dropdown {{ request()->segment(1) == 'kriteria' || request()->segment(1) == 'bobot-kriteria' ? 'active' : '' }}">
              <a href="#" data-toggle="dropdown" class="nav-link has-dropdown"><i class="fas fa-list"></i><span>Kriteria</span></a>
              <ul class="dropdown-menu">
                <li class="nav-item {{ request()->segment(1) == 'kriteria' ? 'active' : '' }}"><a href="{{ route('kriteria.index') }}" class="nav-link">Kriteria</a></li>
                <li class="nav-item {{ request()->segment(1) == 'bobot-kriteria' ? 'active' : '' }}"><a href="{{ route('bobot-kriteria.index') }}" class="nav-link">Bobot Kriteria</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown {{ request()->segment(1) == 'sub-kriteria' || request()->segment(1) == 'bobot-sub-kriteria' ? 'active' : '' }}">
              <a href="#" data-toggle="dropdown" class="nav-link has-dropdown"><i class="fas fa-list"></i><span>Sub Kriteria</span></a>
              <ul class="dropdown-menu">
                <li class="nav-item {{ request()->segment(1) == 'sub-kriteria' ? 'active' : '' }}"><a href="{{ route('sub-kriteria.index') }}" class="nav-link">Sub Kriteria</a></li>
                <li class="nav-item {{ request()->segment(1) == 'bobot-sub-kriteria' ? 'active' : '' }}"><a href="{{ route('bobot-sub-kriteria.index') }}" class="nav-link">Bobot Sub Kriteria</a></li>
              </ul>
            </li>
            <li class="nav-item {{ request()->segment(1) == 'alternatif' ? 'active' : '' }}">
              <a href="{{ route('alternatif.index') }}" class="nav-link"><i class="fas fa-user-tag"></i><span>Alternatif</span></a>
            </li>
            @endif
            @if(Auth::user()->role == 'mhs')
            <li class="nav-item {{ request()->segment(1) == 'rekomendasi' ? 'active' : '' }}">
              <a href="{{ route('rekomendasi.index') }}" class="nav-link"><i class="fas fa-users"></i><span>Rekomendasi</span></a>
            </li>
            @endif
          </ul>
        </div>
      </nav>

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>

      <footer class="main-footer">
        <div class="footer-left">
          UNIMA TI {{ now()->year }}
        </div>
        <div class="footer-right">

        </div>
      </footer>

      @include('sweetalert::alert')
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- Datatables -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.all.min.js"></script>

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <script>
    $(function() {
      /*------------------------------------------ Pass Header Token --------------------------------------------*/
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    });
  </script>

  @stack('scripts')
</body>

</html>