<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Configuración</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.usuarios.index') }}">Personal</a>
                        </li>
                        <li class="breadcrumb-item active">Movimientos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="movimientos" data-user-id="{{ $user->id }}">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user me-3"
                                src="{{ $user->avatar ?? asset('assets/images/users/avatar-1.jpg') }}" alt="Avatar"
                                style="width: 64px; height: 64px; object-fit: cover;" />
                            <div>
                                <h5 class="mb-1">{{ $user->username }}</h5>
                                <div class="text-muted small mb-1">{{ $user->email }}</div>
                                <div>
                                    @foreach ($userRoles as $rol)
                                        <span class="badge bg-soft-primary text-primary">{{ $rol }}</span>
                                    @endforeach
                                    @if ($user->branch)
                                        <span class="badge bg-soft-success text-success ms-1">
                                            <i class="ri-store-line me-1"></i>{{ $user->branch->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.usuarios.edit', $user->id) }}" class="btn btn-soft-info btn-sm">
                                <i class="ri-edit-line me-1"></i>Editar
                            </a>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light btn-sm">
                                <i class="ri-arrow-left-line me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end text-primary">
                        <i class="ri-calendar-event-line widget-icon"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0" title="Citas como veterinario">Citas asignadas</h6>
                    <h3 class="mb-0 mt-1">{{ $citasVeterinario }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end text-info">
                        <i class="ri-user-add-line widget-icon"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">Citas registradas</h6>
                    <h3 class="mb-0 mt-1">{{ $citasCreadas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end text-success">
                        <i class="ri-syringe-line widget-icon"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">Vacunas</h6>
                    <h3 class="mb-0 mt-1">{{ $vacunas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end text-warning">
                        <i class="ri-scissors-cut-line widget-icon"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">Cirugías</h6>
                    <h3 class="mb-0 mt-1">{{ $cirugias }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-citas-vet" role="tab">
                                Citas (veterinario)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-citas-creadas" role="tab">
                                Registradas por él
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-vacunas" role="tab">
                                Vacunas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-cirugias" role="tab">
                                Cirugías
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-citas-vet" role="tabpanel">
                            <table id="table-citas-vet" class="table table-borderless dt-responsive nowrap w-100">
                                <thead>
                                    <tr style="border-bottom: 2px solid #212529;">
                                        <th>#</th>
                                        <th>Mascota</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Servicio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab-citas-creadas" role="tabpanel">
                            <table id="table-citas-creadas" class="table table-borderless dt-responsive nowrap w-100">
                                <thead>
                                    <tr style="border-bottom: 2px solid #212529;">
                                        <th>#</th>
                                        <th>Mascota</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Veterinario</th>
                                        <th>Servicio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab-vacunas" role="tabpanel">
                            <table id="table-vacunas" class="table table-borderless dt-responsive nowrap w-100">
                                <thead>
                                    <tr style="border-bottom: 2px solid #212529;">
                                        <th>#</th>
                                        <th>Mascota</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Próx. dosis</th>
                                        <th>Estado pago</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab-cirugias" role="tabpanel">
                            <table id="table-cirugias" class="table table-borderless dt-responsive nowrap w-100">
                                <thead>
                                    <tr style="border-bottom: 2px solid #212529;">
                                        <th>#</th>
                                        <th>Mascota</th>
                                        <th>Tipo de cirugía</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/movimientos.js') }}"></script>
    @endpush
</x-app-layout>
