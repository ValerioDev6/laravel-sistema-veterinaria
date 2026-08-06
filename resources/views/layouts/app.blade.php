<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>{{ $title ?? 'Sistema Veterinaria' }} | {{ config('app.name', 'Sistema Veterinaria') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt=""
                                        height="22" />
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt=""
                                        height="17" />
                                </span>
                            </a>

                            <a href="{{ route('dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt=""
                                        height="22" />
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-light.png') }}" alt=""
                                        height="17" />
                                </span>
                            </a>
                        </div>

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <!-- dark mode -->
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class="bx bx-moon fs-22"></i>
                            </button>
                        </div>

                        <!-- notifications -->
                        <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-bell fs-22"></i>
                                <span
                                    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">0<span
                                        class="visually-hidden">unread notifications</span></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-2 ps-2" data-simplebar style="max-height: 300px;">
                                    <div class="text-center py-4 text-muted">
                                        No tienes notificaciones.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- user dropdown -->
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ optional(Auth::user())->avatar ?? asset('assets/images/users/avatar-1.jpg') }}"
                                        alt="Header Avatar" />
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ optional(Auth::user())->username }}</span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ optional(Auth::user())->email }}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Hola, {{ optional(Auth::user())->username ?? 'Usuario' }}!
                                </h6>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Perfil</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                        <span class="align-middle">Cerrar sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22" />
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17" />
                    </span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22" />
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17" />
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title">
                            <span data-key="t-general">General</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                                <i class="ri-dashboard-2-line"></i>
                                <span data-key="t-dashboard">Dashboard</span>
                            </a>
                        </li>

                        <li class="menu-title">
                            <span data-key="t-customers">Clientes</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarOwners" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarOwners">
                                <i class="ri-user-heart-line"></i>
                                <span data-key="t-owners">Propietarios</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarOwners">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-owners-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-owners-create">Nuevo
                                            Propietario</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarPets" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarPets">
                                <i class="ri-user-heart-line"></i>
                                <span data-key="t-pets">Pacientes</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarPets">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-pets-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-pets-create">Nuevo
                                            Paciente</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-pets-profile">Ficha del
                                            Paciente</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="menu-title">
                            <span data-key="t-clinic">Clínica</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAppointments" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarAppointments">
                                <i class="ri-calendar-event-line"></i>
                                <span data-key="t-appointments">Citas</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAppointments">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-appointments-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-appointments-create">Nueva
                                            Cita</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link"
                                            data-key="t-appointments-calendar">Calendario</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarVaccines" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarVaccines">
                                <i class="ri-syringe-line"></i>
                                <span data-key="t-vaccines">Vacunas</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarVaccines">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-vaccines-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-vaccines-create">Registrar
                                            Vacuna</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-vaccine-types">Tipos de
                                            Vacuna</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarSurgeries" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarSurgeries">
                                <i class="ri-scissors-cut-line"></i>
                                <span data-key="t-surgeries">Cirugías</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarSurgeries">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-surgeries-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-surgeries-create">Registrar
                                            Cirugía</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="ri-file-list-3-line"></i>
                                <span data-key="t-medical-records">Historial Clínico</span>
                            </a>
                        </li>

                        <li class="menu-title">
                            <span data-key="t-finance">Facturación</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarInvoices" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarInvoices">
                                <i class="ri-file-list-3-line"></i>
                                <span data-key="t-invoices">Facturas</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarInvoices">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-invoices-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-invoices-create">Nueva
                                            Factura</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="ri-bank-card-line"></i>
                                <span data-key="t-payments">Pagos</span>
                            </a>
                        </li>

                        <li class="menu-title">
                            <span data-key="t-catalog">Catálogo</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarServices" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarServices">
                                <i class="ri-briefcase-line"></i>
                                <span data-key="t-services">Servicios</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarServices">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-services-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-services-create">Nuevo
                                            Servicio</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMedicines" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarMedicines">
                                <i class="ri-first-aid-kit-line"></i>
                                <span data-key="t-medicines">Medicamentos</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMedicines">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-medicines-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-medicines-create">Nuevo
                                            Medicamento</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarSpecies" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarSpecies">
                                <i class="ri-leaf-line"></i>
                                <span data-key="t-species">Especies y Razas</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarSpecies">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-species-list">Especies</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-breeds-list">Razas</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="menu-title">
                            <span data-key="t-config">Configuración</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarBranches" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarBranches">
                                <i class="ri-store-line"></i>
                                <span data-key="t-branches">Sucursales</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarBranches">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-branches-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-branches-create">Nueva
                                            Sucursal</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarStaff" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarStaff">
                                <i class="ri-user-settings-line"></i>
                                <span data-key="t-staff">Personal</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStaff">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-staff-list">Listado</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-staff-create">Nuevo
                                            Usuario</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-schedules">Horarios</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>

            <!-- footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script>
                            © {{ config('app.name', 'Sistema Veterinaria') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Veterinaria | Dashboard
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
