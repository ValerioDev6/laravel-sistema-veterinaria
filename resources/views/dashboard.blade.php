<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">General</a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <label class="form-label mb-0 fw-medium text-muted" for="selectAnio">Año</label>
                <select id="selectAnio" class="form-select w-auto">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected($year == $selectedYear)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Facturado <span class="kpi-anio">{{ $selectedYear }}</span></p>
                            <h2 class="mt-3 ff-secondary fw-semibold text-primary" id="kpi-facturado">S/ 0.00</h2>
                            <p class="text-muted mb-0 fs-12">Facturas emitidas del año</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                                <i class="ri-money-dollar-circle-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Cobrado <span class="kpi-anio">{{ $selectedYear }}</span></p>
                            <h2 class="mt-3 ff-secondary fw-semibold text-success" id="kpi-cobrado">S/ 0.00</h2>
                            <p class="text-muted mb-0 fs-12">Pagos recibidos del año</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle fs-2">
                                <i class="ri-wallet-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Por cobrar</p>
                            <h2 class="mt-3 ff-secondary fw-semibold text-warning" id="kpi-por-cobrar">S/ 0.00</h2>
                            <p class="text-muted mb-0 fs-12">Facturas pendientes y parciales</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded-circle fs-2">
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Citas de hoy</p>
                            <h2 class="mt-3 ff-secondary fw-semibold text-info" id="kpi-citas-hoy">0</h2>
                            <p class="text-muted mb-0 fs-12">{{ now()->format("d/m/Y") }}</p>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded-circle fs-2">
                                <i class="ri-calendar-event-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Pacientes</p>
                            <h4 class="mt-2 ff-secondary fw-semibold mb-0" id="kpi-pacientes">0</h4>
                        </div>
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle fs-5">
                                <i class="ri-user-heart-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Vacunas aplicadas <span class="kpi-anio">{{ $selectedYear }}</span></p>
                            <h4 class="mt-2 ff-secondary fw-semibold mb-0" id="kpi-vacunas">0</h4>
                        </div>
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle fs-5">
                                <i class="ri-syringe-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Cirugías <span class="kpi-anio">{{ $selectedYear }}</span></p>
                            <h4 class="mt-2 ff-secondary fw-semibold mb-0" id="kpi-cirugias">0</h4>
                        </div>
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-danger rounded-circle fs-5">
                                <i class="ri-scissors-cut-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase fw-medium text-muted mb-0">Recordatorios pendientes</p>
                            <h4 class="mt-2 ff-secondary fw-semibold mb-0" id="kpi-recordatorios">0</h4>
                        </div>
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded-circle fs-5">
                                <i class="ri-notification-3-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Ingresos mensuales <span class="kpi-anio">{{ $selectedYear }}</span></h4>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartIngresos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Citas por estado <span class="kpi-anio">{{ $selectedYear }}</span></h4>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartCitasEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Pacientes por especie</h4>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartEspecies"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Top veterinarios <span class="kpi-anio">{{ $selectedYear }}</span></h4>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartTopVets"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/dashboard.js') }}"></script>
    @endpush
</x-app-layout>
