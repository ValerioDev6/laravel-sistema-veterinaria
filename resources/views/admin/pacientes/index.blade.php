<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Clientes</a>
                        </li>
                        <li class="breadcrumb-item active">Pacientes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabBusquedaPaciente" role="tab" aria-selected="true">
                                <i class="ri-search-line me-1"></i>Buscar Paciente
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabFormPaciente" id="tabFormPaciente-tab" role="tab" aria-selected="false">
                                <i class="ri-pencil-line me-1"></i>Formulario de Paciente
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabSpecies" role="tab" aria-selected="false">
                                <i class="ri-leaf-line me-1"></i>Species
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabRazas" role="tab" aria-selected="false">
                                <i class="ri-shapes-line me-1"></i>Razas
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">
                    {{-- Buscar Paciente --}}
                    <div class="tab-pane active" id="tabBusquedaPaciente" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Listado de Pacientes</h4>
                                <button type="button" class="btn btn-primary btn-sm" id="btnNuevaMascota">
                                    <i class="ri-add-line me-1"></i>Nueva Mascota
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3" style="max-width: 420px;">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="search" id="busquedaPaciente" class="form-control" placeholder="Buscar por nombre, especie, propietario…">
                                </div>
                                <table id="table-pacientes" class="table table-borderless dt-responsive nowrap w-100">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #212529;">
                                            <th>ID</th>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Especie</th>
                                            <th>Raza</th>
                                            <th>Propietario</th>
                                            <th>Sexo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario de Paciente --}}
                    <div class="tab-pane fade" id="tabFormPaciente" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0" id="tituloFormPaciente">Nueva Mascota</h4>
                            </div>
                            <div class="card-body">
                                @include('admin.pacientes.partials.form-create')
                            </div>
                        </div>
                    </div>

                    {{-- Species --}}
                    <div class="tab-pane fade" id="tabSpecies" role="tabpanel">
                        @include('admin.species.partials.table', ['modal' => true])
                    </div>

                    {{-- Razas --}}
                    <div class="tab-pane fade" id="tabRazas" role="tabpanel">
                        @include('admin.breeds.partials.table', ['modal' => true])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.species.partials.modal')
    @include('admin.breeds.partials.modal')

    @push('scripts')
        <script src="{{ asset('js/pages/pacientes.js') }}"></script>
        <script src="{{ asset('js/pages/species.js') }}"></script>
        <script src="{{ asset('js/pages/breeds.js') }}"></script>
    @endpush
</x-app-layout>
