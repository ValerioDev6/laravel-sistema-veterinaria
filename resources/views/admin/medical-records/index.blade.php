<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.medical-records.index') }}">Historial Médico</a>
                        </li>
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Historial Médico</h4>
                    <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>Nueva entrada
                    </a>
                </div>
                <div class="card-body">
                    <form id="formFiltros" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="filtro_pet_id" name="pet_id">
                                <option value="">Todas las mascotas</option>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="table-medical-records" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Veterinario</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/medical-records.js') }}"></script>
    @endpush
</x-app-layout>