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
                        <li class="breadcrumb-item active">Veterinarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Listado de Veterinarios</h4>
                    <a href="{{ route('admin.veterinarios.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>Nuevo Veterinario
                    </a>
                </div>
                <div class="card-body">
                    <form id="formFiltrosVeterinarios" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="busquedaVeterinarios">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="search" class="form-control" id="busquedaVeterinarios" name="search" placeholder="Usuario o email…">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-filter-line me-1"></i>Filtrar
                            </button>
                            <button type="button" class="btn btn-light" id="btnLimpiarFiltrosVeterinarios">
                                <i class="ri-eraser-line me-1"></i>Limpiar
                            </button>
                        </div>
                    </form>

                    <table id="table-veterinarios" class="table table-borderless dt-responsive nowrap w-100">
                        <thead>
                            <tr style="border-bottom: 2px solid #212529;">
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Sucursal</th>
                                <th>Horario de atención</th>
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
        <script src="{{ asset('js/pages/veterinarios.js') }}"></script>
    @endpush
</x-app-layout>
