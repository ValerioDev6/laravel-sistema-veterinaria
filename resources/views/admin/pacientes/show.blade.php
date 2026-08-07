<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.pacientes.index') }}">Pacientes</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $paciente->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($paciente->photo)
                        <img src="{{ $paciente->photo }}" alt="Foto" class="img-fluid rounded-circle avatar-xl mb-3">
                    @else
                        <div class="avatar-xl mx-auto mb-3 rounded-circle bg-soft-primary d-flex align-items-center justify-content-center">
                            <i class="ri-paw-line fs-1 text-primary"></i>
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $paciente->name }}</h5>
                    <p class="text-muted mb-3">{{ $paciente->species?->name }}@if ($paciente->breed) · {{ $paciente->breed->name }}@endif</p>

                    <div class="table-responsive">
                        <table class="table table-sm mb-0 text-start">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Propietario</td>
                                    <td>{{ $paciente->owner?->first_name }} {{ $paciente->owner?->last_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Teléfono</td>
                                    <td>{{ $paciente->owner?->phone }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nacimiento</td>
                                    <td>{{ $paciente->birth_date?->format('d/m/Y') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Sexo</td>
                                    <td>{{ ucfirst($paciente->gender) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Color</td>
                                    <td>{{ $paciente->color ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Peso</td>
                                    <td>{{ $paciente->weight ? $paciente->weight . ' kg' : '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="{{ route('admin.pacientes.edit', $paciente->id) }}" class="btn btn-soft-primary btn-sm">
                            <i class="ri-pencil-line me-1"></i>Editar
                        </a>
                        <a href="{{ route('admin.owners.show', $paciente->owner_id) }}" class="btn btn-soft-info btn-sm">
                            <i class="ri-user-line me-1"></i>Propietario
                        </a>
                    </div>

                    @if ($paciente->medical_notes)
                        <hr>
                        <h6 class="text-muted text-start">Notas médicas</h6>
                        <p class="text-start mb-0">{{ $paciente->medical_notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-records" role="tab" data-tab="records">
                                Historial médico
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-vacunas" role="tab" data-tab="vacunas">
                                Vacunas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-cirugias" role="tab" data-tab="cirugias">
                                Cirugías
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="tabPaciente">
                        <div class="tab-pane fade show active" id="tab-records" role="tabpanel"></div>
                        <div class="tab-pane fade" id="tab-vacunas" role="tabpanel"></div>
                        <div class="tab-pane fade" id="tab-cirugias" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/paciente-ficha.js') }}"></script>
    @endpush
</x-app-layout>