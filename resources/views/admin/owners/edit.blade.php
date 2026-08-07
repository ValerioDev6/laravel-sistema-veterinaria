<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.owners.index') }}">Propietarios</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Propietario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Propietario</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarOwner" data-id="{{ $owner->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="first_name">Nombres</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $owner->first_name }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="last_name">Apellidos</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $owner->last_name }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="type_documento">Tipo de documento</label>
                                <select class="form-select" id="type_documento" name="type_documento">
                                    <option value="">Seleccionar</option>
                                    <option value="DNI" @selected($owner->type_documento == 'DNI')>DNI</option>
                                    <option value="CE" @selected($owner->type_documento == 'CE')>Carné de Extranjería</option>
                                    <option value="Pasaporte" @selected($owner->type_documento == 'Pasaporte')>Pasaporte</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="n_documento">N° de documento</label>
                                <input type="text" class="form-control" id="n_documento" name="n_documento" value="{{ $owner->n_documento }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="phone">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $owner->phone }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email <span class="text-muted">(opcional)</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $owner->email }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="city">Ciudad <span class="text-muted">(opcional)</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ $owner->city }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Dirección <span class="text-muted">(opcional)</span></label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $owner->address }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarOwner">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.owners.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/owners.js') }}"></script>
    @endpush
</x-app-layout>