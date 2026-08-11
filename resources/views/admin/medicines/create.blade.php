<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.medicines.index') }}">Medicamentos</a>
                        </li>
                        <li class="breadcrumb-item active">Nuevo Medicamento</li>
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
                        <i class="ri-medicine-bottle-line me-1 text-primary"></i>Nuevo Medicamento
                    </h4>
                    <span class="badge bg-info-subtle text-info">Registro de medicamento</span>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-xl-6">
                            <form id="formCrearMedicine">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="id">ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                            <input type="text" class="form-control" id="id" value="Autogenerado" disabled>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-medicine-bottle-line"></i></span>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Amoxicilina 500 mg" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="quantity">Stock <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-box-3-line"></i></span>
                                            <input type="number" class="form-control" id="quantity" name="quantity" value="0" min="0" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="unit_cost">Costo Unitario (S/) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
                                            <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" min="0" placeholder="0.00" required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
                                    <a href="{{ route('admin.medicines.index') }}" class="btn btn-light waves-effect waves-light">
                                        <i class="ri-close-line me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarMedicine">
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
        <script src="{{ asset('js/pages/medicines.js') }}"></script>
    @endpush
</x-app-layout>