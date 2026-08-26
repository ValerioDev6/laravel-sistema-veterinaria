<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.usuarios.index') }}">Personal</a>
                        </li>
                        <li class="breadcrumb-item active">Nuevo Usuario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearUsuario" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="username">Nombre de usuario</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Ej. maria.gonzales">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ej. maria@veterinaria.com">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="role">Rol</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">Seleccionar rol</option>
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="branch_id">Sucursal</label>
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Sin sede asignada</option>
                                    @foreach ($branches as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="phone">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="type_documento">Tipo documento</label>
                                <select class="form-select" id="type_documento" name="type_documento">
                                    <option value="">Seleccionar</option>
                                    <option value="DNI">DNI</option>
                                    <option value="CE">CE</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="RUC">RUC</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="n_documento">N° documento</label>
                                <input type="text" class="form-control" id="n_documento" name="n_documento">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="birthday">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="birthday" name="birthday">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="avatar">Foto (opcional)</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarUsuario">
                                <i class="ri-save-line me-1"></i>Guardar
                            </button>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/usuarios.js') . "?v=2" }}"></script>
    @endpush
</x-app-layout>