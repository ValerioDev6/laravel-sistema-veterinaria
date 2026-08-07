<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.vaccine-types.index') }}">Tipos de Vacuna</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Tipo de Vacuna</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Tipo de Vacuna</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarVaccineType" data-id="{{ $vaccineType->id }}">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $vaccineType->name }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="species_id">Especie <span class="text-muted">(opcional)</span></label>
                            <select class="form-select" id="species_id" name="species_id">
                                <option value="">Aplica a todas las especies</option>
                                @foreach ($species as $value => $label)
                                    <option value="{{ $value }}" @selected($vaccineType->species_id == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarVaccineType">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.vaccine-types.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/vaccine-types.js') }}"></script>
    @endpush
</x-app-layout>