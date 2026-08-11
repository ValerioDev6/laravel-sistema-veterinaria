<x-app-layout>
    <style>
        .medical-card {
            transition: box-shadow .2s ease, transform .2s ease;
            height: 100%;
        }
        .medical-card:hover {
            box-shadow: 0 .35rem 1.1rem rgba(0,0,0,.12) !important;
            transform: translateY(-2px);
        }
        .notas-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

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
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="card-title mb-0">Historial Médico</h4>
                    <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>Nueva entrada
                    </a>
                </div>
                <div class="card-body">
                    {{-- Búsqueda por mascota --}}
                    <form id="formFiltros" class="row g-2 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small text-muted mb-1" for="filtro_pet_id">Mascota</label>
                            <select class="form-select" id="filtro_pet_id" name="pet_id">
                                <option value="">Selecciona una mascota</option>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7 d-flex align-items-end">
                            <p class="text-muted small mb-0">
                                <i class="ri-paw-line me-1 text-primary"></i>
                                Selecciona una mascota para ver todo su historial médico.
                            </p>
                        </div>
                    </form>

                    {{-- Tabs por tipo de evento --}}
                    <input type="hidden" id="historialPetId">
                    <div class="mb-3 d-none" id="bloqueHistorial">
                        <ul class="nav nav-tabs nav-tabs-custom gap-2 mb-3" role="tablist" id="tabsHistorial">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-todos" role="tab" data-tipo="todos">
                                    Todos <span class="badge bg-soft-primary text-primary ms-1" id="count-todos">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-citas" role="tab" data-tipo="citas">
                                    <i class="ri-calendar-check-line me-1"></i>Citas <span class="badge bg-soft-primary text-primary ms-1" id="count-citas">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-vacunas" role="tab" data-tipo="vacunas">
                                    <i class="ri-syringe-line me-1"></i>Vacunas <span class="badge bg-soft-primary text-primary ms-1" id="count-vacunas">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-cirugias" role="tab" data-tipo="cirugias">
                                    <i class="ri-scissors-2-line me-1"></i>Cirugías <span class="badge bg-soft-primary text-primary ms-1" id="count-cirugias">0</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="contentHistorial">
                            <div class="tab-pane fade show active" id="tab-todos" role="tabpanel"></div>
                            <div class="tab-pane fade" id="tab-citas" role="tabpanel"></div>
                            <div class="tab-pane fade" id="tab-vacunas" role="tabpanel"></div>
                            <div class="tab-pane fade" id="tab-cirugias" role="tabpanel"></div>
                        </div>
                    </div>

                    {{-- Empty state: sin mascota / sin resultados --}}
                    <div id="estadoVacio" class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3 rounded-circle bg-soft-light d-flex align-items-center justify-content-center">
                            <i class="ri-file-history-line fs-1 text-muted"></i>
                        </div>
                        <h5 class="mb-1">Sin historial cargado</h5>
                        <p class="text-muted mb-0">Selecciona una mascota para ver su historial médico.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/medical-records.js') }}"></script>
    @endpush
</x-app-layout>