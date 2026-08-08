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
                        <li class="breadcrumb-item active">Propietarios</li>
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
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabBuscarPropietario" role="tab" aria-selected="true">
                                <i class="ri-search-line me-1"></i>Buscar Propietario
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabFormPropietario" id="tabFormPropietario-tab" role="tab" aria-selected="false">
                                <i class="ri-pencil-line me-1"></i>Formulario de Propietario
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabFichaPropietario" id="tabFichaPropietario-tab" role="tab" aria-selected="false">
                                <i class="ri-file-user-line me-1"></i>Ficha del Propietario
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">
                    <div class="tab-pane active" id="tabBuscarPropietario" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Listado de Propietarios</h4>
                                <button type="button" class="btn btn-primary btn-sm" id="btnNuevoPropietario">
                                    <i class="ri-add-line me-1"></i>Nuevo Propietario
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3" style="max-width: 420px;">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="search" id="busquedaPropietario" class="form-control" placeholder="Buscar por nombre, documento, teléfono o email…">
                                </div>
                                <table id="table-owners" class="table table-borderless dt-responsive nowrap w-100">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #212529;">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Documento</th>
                                            <th>Teléfono</th>
                                            <th>Email</th>
                                            <th>Mascotas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tabFormPropietario" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0" id="tituloFormPropietario">Nuevo Propietario</h4>
                            </div>
                            <div class="card-body">
                                @include('admin.owners.partials.form')
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tabFichaPropietario" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0" id="tituloFichaPropietario">Ficha del Propietario</h4>
                                <button type="button" class="btn btn-light btn-sm" id="btnRegresarFicha">
                                    <i class="ri-arrow-left-line me-1"></i>Regresar
                                </button>
                            </div>
                            <div class="card-body" id="contenidoFichaPropietario">
                                <div class="text-center text-muted py-4">
                                    <i class="ri-file-user-line fs-1"></i>
                                    <p class="mb-0 mt-2">Selecciona un propietario con el botón de la ficha para ver su detalle.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/owners.js') }}"></script>
    @endpush
</x-app-layout>
