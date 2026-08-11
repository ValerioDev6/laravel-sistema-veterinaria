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
                        <li class="breadcrumb-item active">Nuevo Servicio</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">
                        <i class="ri-stethoscope-line me-1 text-primary"></i>Nuevo Servicio
                    </h4>
                    <span class="badge bg-info-subtle text-info">Registro de servicio</span>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-xl-8">
                            <form id="formCrearService">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="id">ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                            <input type="text" class="form-control" id="id" value="Autogenerado" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-stethoscope-line"></i></span>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Consulta general" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label" for="category">Categoría <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-price-tag-3-line"></i></span>
                                            <select class="form-select" id="category" name="category" required>
                                                @foreach ($categories as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label" for="base_price">Precio base (S/) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
                                            <input type="number" step="0.01" min="0" class="form-control" id="base_price" name="base_price" placeholder="0.00" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label" for="duration_minutes">Duración (minutos) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-time-line"></i></span>
                                            <input type="number" min="1" class="form-control" id="duration_minutes" name="duration_minutes" value="30" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="description">Descripción <span class="text-muted">(opcional)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-file-text-line"></i></span>
                                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
                                    <a href="{{ route('admin.services.index') }}" class="btn btn-light waves-effect waves-light">
                                        <i class="ri-close-line me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarService">
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

    @push('scripts')
        <script src="{{ asset('js/pages/services.js') }}"></script>
    @endpush
</x-app-layout>