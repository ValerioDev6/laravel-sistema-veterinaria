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
                        <li class="breadcrumb-item active">Editar Medicamento</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Medicamento</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarMedicine" data-id="{{ $medicine->id }}">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $medicine->name }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="quantity">Stock</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $medicine->quantity }}" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="unit_cost">Costo Unitario (S/)</label>
                            <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" min="0" value="{{ $medicine->unit_cost }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarMedicine">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.medicines.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/medicines.js') }}"></script>
    @endpush
</x-app-layout>