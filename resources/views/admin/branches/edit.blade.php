<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.branches.index') }}">Sucursales</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Sucursal</li>
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
                        <i class="ri-store-line me-1 text-primary"></i>Editar Sucursal
                    </h4>
                    <span class="badge bg-info-subtle text-info">ID #{{ $branch->id }}</span>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-xl-8">
                            <form id="formEditarBranch" data-id="{{ $branch->id }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="id">ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                            <input type="text" class="form-control" id="id" value="#{{ $branch->id }}" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-store-line"></i></span>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $branch->name }}" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="address">Dirección <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-map-pin-line"></i></span>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ $branch->address }}" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="city">Ciudad <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-building-2-line"></i></span>
                                            <input type="text" class="form-control" id="city" name="city" value="{{ $branch->city }}" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="phone">Teléfono <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $branch->phone }}" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
                                    <a href="{{ route('admin.branches.index') }}" class="btn btn-light waves-effect waves-light">
                                        <i class="ri-close-line me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnActualizarBranch">
                                        <i class="ri-save-3-line me-1"></i>Actualizar
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
        <script src="{{ asset('js/pages/branches.js') }}"></script>
    @endpush
</x-app-layout>