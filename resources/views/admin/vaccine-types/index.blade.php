<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Catálogo</a>
                        </li>
                        <li class="breadcrumb-item active">Tipos de Vacuna</li>
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
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabListadoVaccineTypes" role="tab" aria-selected="true">
                                <i class="ri-list-unordered me-1"></i>Listado
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabFormularVacunas" id="tabFormularVacunas-tab" role="tab" aria-selected="false">
                                <i class="ri-syringe-line me-1"></i>Formular Vacunas
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">
                    {{-- Listado --}}
                    <div class="tab-pane active" id="tabListadoVaccineTypes" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Listado de Tipos de Vacuna</h4>
                                <button type="button" class="btn btn-primary btn-sm" id="btnNuevoVaccineType">
                                    <i class="ri-add-line me-1"></i>Nuevo Tipo de Vacuna
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="formFiltrosVaccineTypes" class="row g-3 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label" for="busquedaVaccineTypes">Buscar</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                                            <input type="search" class="form-control" id="busquedaVaccineTypes" name="search" placeholder="Nombre o especie…">
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="ri-filter-line me-1"></i>Filtrar
                                        </button>
                                        <button type="button" class="btn btn-light" id="btnLimpiarFiltros">
                                            <i class="ri-eraser-line me-1"></i>Limpiar
                                        </button>
                                    </div>
                                </form>

                                <table id="table-vaccine-types" class="table table-borderless dt-responsive nowrap w-100">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #212529;">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Especie</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Formular Vacunas --}}
                    <div class="tab-pane fade" id="tabFormularVacunas" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">
                                    <i class="ri-virus-line me-1 text-primary"></i>Formular Vacunas
                                </h4>
                                <span class="badge bg-info-subtle text-info">Nuevo tipo de vacuna</span>
                            </div>
                            <div class="card-body">
                                <form id="formCrearVaccineType">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label" for="id">ID</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                                    <input type="text" class="form-control" id="id" value="Autogenerado" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label class="form-label" for="name">
                                                    Nombre <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ri-syringe-line"></i></span>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Séxtuple canina" required>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="base_price">
                                                    Precio base (S/) <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
                                                    <input type="number" step="0.01" min="0" class="form-control" id="base_price" name="base_price" placeholder="0.00" value="0.00" required>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="species_id">Especie <span class="text-muted">(opcional)</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="ri-test-tube-line"></i></span>
                                                    <select class="form-select" id="species_id" name="species_id">
                                                        <option value="">Aplica a todas las especies</option>
                                                        @foreach ($species as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
                                        <a href="{{ route('admin.vaccine-types.index') }}" class="btn btn-light waves-effect waves-light">
                                            <i class="ri-close-line me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarVaccineType">
                                            <i class="ri-save-3-line me-1"></i>Guardar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/vaccine-types.js') }}"></script>
    @endpush
</x-app-layout>
