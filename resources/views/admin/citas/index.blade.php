<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Clínica</a>
                        </li>
                        <li class="breadcrumb-item active">Citas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Listado de Citas</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.citas.calendar') }}" class="btn btn-soft-info btn-sm">
                            <i class="ri-calendar-line me-1"></i>Calendario
                        </a>
                        <a href="{{ route('admin.citas.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i>Nueva Cita
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formFiltrosCitas" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="filter-busqueda">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="search" class="form-control" id="busquedaCitas" name="search" placeholder="Mascota, veterinario o especie…">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="filter-veterinario">Veterinario</label>
                            <select class="form-select" id="filter-veterinario" name="veterinarian_id">
                                <option value="">Todos</option>
                                @foreach ($veterinarians as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="filter-especie">Especie</label>
                            <select class="form-select" id="filter-especie" name="species_id">
                                <option value="">Todas</option>
                                @foreach ($species as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="filter-fecha-desde">Fecha desde</label>
                            <input type="date" class="form-control" id="filter-fecha-desde" name="appointment_date_from">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="filter-fecha-hasta">Fecha hasta</label>
                            <input type="date" class="form-control" id="filter-fecha-hasta" name="appointment_date_to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="filter-estado">Estado</label>
                            <select class="form-select" id="filter-estado" name="status">
                                <option value="">Todos</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-filter-line me-1"></i>Filtrar
                            </button>
                            <button type="button" class="btn btn-light" id="btnLimpiarFiltros">
                                <i class="ri-eraser-line me-1"></i>Limpiar
                            </button>
                        </div>
                    </form>

                    <table id="table-citas" class="table table-borderless dt-responsive nowrap w-100">
                        <thead>
                            <tr style="border-bottom: 2px solid #212529;">
                                <th>ID</th>
                                <th>Mascota</th>
                                <th>Veterinario</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/citas.js') }}"></script>
    @endpush
</x-app-layout>