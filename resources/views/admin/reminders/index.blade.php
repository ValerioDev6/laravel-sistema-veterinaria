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
                        <li class="breadcrumb-item active">Recordatorios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Listado de Recordatorios</h4>
                </div>
                <div class="card-body">
                    <form id="formFiltrosReminders" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="busquedaReminders">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="search" class="form-control" id="busquedaReminders" name="search" placeholder="Mascota, mensaje o tipo…">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="filter-mascota">Mascota</label>
                            <select class="form-select" id="filter-mascota" name="pet_id">
                                <option value="">Todas</option>
                                @foreach ($pacientes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="filter-tipo">Tipo</label>
                            <select class="form-select" id="filter-tipo" name="tipo">
                                <option value="">Todos</option>
                                <option value="cita">Cita</option>
                                <option value="vacuna">Vacuna</option>
                                <option value="cirugia">Cirugía</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="filter-estado">Estado</label>
                            <select class="form-select" id="filter-estado" name="status">
                                <option value="">Todos</option>
                                @foreach ($estados as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
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

                    <table id="table-reminders" class="table table-borderless dt-responsive nowrap w-100">
                        <thead>
                            <tr style="border-bottom: 2px solid #212529;">
                                <th>ID</th>
                                <th>Mascota</th>
                                <th>Tipo</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
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
        <script src="{{ asset('js/pages/reminders.js') }}"></script>
    @endpush
</x-app-layout>