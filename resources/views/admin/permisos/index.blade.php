<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.roles.index') }}">Roles</a>
                        </li>
                        <li class="breadcrumb-item active">Permisos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Listado de Permisos</h4>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-soft-primary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Volver a Roles
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary d-flex align-items-center mb-3" role="alert">
                        <i class="ri-information-line me-2"></i>
                        Lista de solo lectura: los permisos se crean en el seeder y se asignan desde cada rol.
                    </div>

                    <form id="formFiltrosPermisos" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="busquedaPermisos">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="search" class="form-control" id="busquedaPermisos" name="search" placeholder="Permiso (ej. list_pet)…">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="grupoPermisos">Módulo</label>
                            <select class="form-select" id="grupoPermisos" name="grupo">
                                <option value="">Todos</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo }}">{{ $grupo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-filter-line me-1"></i>Filtrar
                            </button>
                            <button type="button" class="btn btn-light" id="btnLimpiarFiltrosPermisos">
                                <i class="ri-eraser-line me-1"></i>Limpiar
                            </button>
                        </div>
                    </form>

                    <table id="table-permisos" class="table table-borderless dt-responsive nowrap w-100">
                        <thead>
                            <tr style="border-bottom: 2px solid #212529;">
                                <th>Permiso</th>
                                <th>Descripción</th>
                                <th>Módulo</th>
                                <th>Guard</th>
                                <th>Roles</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/permisos.js') }}"></script>
    @endpush
</x-app-layout>
