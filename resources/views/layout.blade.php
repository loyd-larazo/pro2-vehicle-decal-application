<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PRO2 Vehicle Decal Application System</title>
      
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="/css/layout.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="/css/app.css" rel="stylesheet">

    <script src="/js/jquery-3.6.1.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="/js/scripts.js"></script>
    <script>
      $(function() {
        $(document).on('keydown', '.password', function(e) {
          if (e.keyCode == 32) return false;
        });
      });
    </script>
  </head>
  <body>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
      <!-- Navbar Brand-->
      <a class="navbar-brand ps-3 text-wrap" href="/">
        PRO2 Vehicle Decal
      </a>
      <!-- Sidebar Toggle-->
      <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
      <!-- Navbar Search-->
      <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      </div>
      <!-- Navbar-->
      <div class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 text-white">
        {{ Session::get('fullname') }}
      </div>
    </nav>
    <div id="layoutSidenav">
      <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
          <div class="sb-sidenav-menu">
            <div class="nav">
              @if (Session::get('userType') && in_array(Session::get('userType'), ["issuer", "admin"]))
                <div class="sb-sidenav-menu-heading">Approval</div>
                @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
                  <a class="nav-link {{ in_array(request()->route()->getName(), ['applicants'])  ? 'active' : '' }}" href="/applicants">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users-rectangle"></i></div>
                    <span class="position-relative">
                      Applicants
                      @if (Session::get('pending_applicants'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                          {{ Session::get('pending_applicants') }}
                        </span>
                      @endif
                    </span>
                  </a>

                  <a class="nav-link {{ in_array(request()->route()->getName(), ['vehicles'])  ? 'active' : '' }}" href="/vehicles">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-car"></i></div>
                    <span class="position-relative">
                      Vehicles
                      @if (Session::get('pending_vehicles'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                          {{ Session::get('pending_vehicles') }}
                        </span>
                      @endif
                    </span>
                  </a>
                @endif

                <a class="nav-link {{ in_array(request()->route()->getName(), ['release'])  ? 'active' : '' }}" href="/release">
                  <div class="sb-nav-link-icon"><i class="fa-regular fa-credit-card"></i></div>
                  <span class="position-relative">
                    For Release
                    @if (Session::get('pending_release'))
                      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ Session::get('pending_release') }}
                      </span>
                    @endif
                  </span>
                </a>
              @endif

              @if (Session::get('userType') && in_array(Session::get('userType'), ["admin"]))
                <div class="sb-sidenav-menu-heading">App Users</div>
                <a class="nav-link {{ isset($userType) && $userType == 'users' ? 'active' : '' }}" href="/app/users">
                  <div class="sb-nav-link-icon"><i class="fa-solid fa-users-line"></i></div>
                  Users
                </a>
                <a class="nav-link {{ isset($userType) && $userType == 'issuers' ? 'active' : '' }}" href="/app/issuers">
                  <div class="sb-nav-link-icon"><i class="fa-solid fa-user-group"></i></div>
                  Issuers
                </a>
                <a class="nav-link {{ isset($userType) && $userType == 'admins' ? 'active' : '' }}" href="/app/admins">
                  <div class="sb-nav-link-icon"><i class="fa-solid fa-user-tie"></i></div>
                  Admins
                </a>
              @endif

              <div class="sb-sidenav-menu-heading">Settings</div>
              @if (Session::get('userType') && in_array(Session::get('userType'), ["user"]))
                <a class="nav-link {{ in_array(request()->route()->getName(), ['profileVehicles'])  ? 'active' : '' }}" href="/profile/vehicles">
                  <div class="sb-nav-link-icon"><i class="fa-solid fa-car"></i></div>
                  <span class="position-relative">
                    My Vehicles
                  </span>
                </a>
              @endif
              <a class="nav-link {{ in_array(request()->route()->getName(), ['profile'])  ? 'active' : '' }}" href="/profile">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-user-gear"></i></div>
                <span class="position-relative">
                  Profile Settings
                </span>
              </a>
              <a class="nav-link" href="/logout">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
                <span class="position-relative">Logout</span>
              </a>
            </div>
          </div>
        </nav>
      </div>
      <div id="layoutSidenav_content">
        <main>
          <div class="container-fluid px-4">
            
            @yield('content')
            
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
