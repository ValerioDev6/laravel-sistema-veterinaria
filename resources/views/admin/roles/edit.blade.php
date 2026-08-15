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
                        <li class="breadcrumb-item active">Editar Rol</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Rol: {{ $role->name }}</h4>
                </div>
                <div class="card-body">
                    @if ($role->name === 'Super-Admin')
                        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                            <i class="ri-information-line me-2"></i>
                            El rol Super-Admin no se puede eliminar. Puedes editar su nombre y permisos.
                        </div>
                    @endif

                    <form id="formEditarRol" data-id="{{ $role->id }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="name">Nombre del rol <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <h5 class="mb-0">Permisos del rol</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-soft-success btn-sm" id="btnSeleccionarTodos">
                                    <i class="ri-check-double-line me-1"></i>Seleccionar todos
                                </button>
                                <button type="button" class="btn btn-soft-light btn-sm" id="btnLimpiarPermisos">
                                    <i class="ri-close-line me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                        <hr class="mt-1 mb-3">

                        <div class="row g-3">
                            @foreach ($grupos as $grupo => $items)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">{{ $grupo }}</h6>
                                        </div>
                                        <div class="card-body py-2" style="max-height: 220px; overflow-y: auto;">
                                            @foreach ($items as $item)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input permiso-check" type="checkbox"
                                                        name="permissions[]" value="{{ $item['id'] }}"
                                                        id="permiso-{{ $item['id'] }}"
                                                        @checked(in_array($item['id'], $permisosActuales))>
                                                    <label class="form-check-label" for="permiso-{{ $item['id'] }}">
                                                        {{ $item['label'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex gap-2 pt-3 mt-2 border-top">
                            <button type="submit" class="btn btn-primary" id="btnActualizarRol">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/roles.js') }}"></script>
    @endpush
</x-app-layout>
