<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.services.index') }}">Servicios</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Servicio</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Servicio</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarService" data-id="{{ $service->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $service->name }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="category">Categoría</label>
                                <select class="form-select" id="category" name="category">
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected($service->category == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="base_price">Precio base (S/)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="base_price" name="base_price" value="{{ $service->base_price }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="duration_minutes">Duración (minutos)</label>
                                <input type="number" min="1" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ $service->duration_minutes }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label" for="description">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $service->description }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarService">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/services.js') }}"></script>
    @endpush
</x-app-layout>