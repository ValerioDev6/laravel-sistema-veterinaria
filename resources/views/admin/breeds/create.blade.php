<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.breeds.index') }}">Razas</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Raza</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Nueva Raza</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearBreed">
                        <div class="mb-3">
                            <label class="form-label" for="species_id">Especie</label>
                            <select class="form-select" id="species_id" name="species_id">
                                <option value="">Seleccionar especie</option>
                                @foreach ($species as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Labrador Retriever">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarBreed">
                                <i class="ri-save-line me-1"></i>Guardar
                            </button>
                            <a href="{{ route('admin.breeds.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/breeds.js') }}"></script>
    @endpush
</x-app-layout>