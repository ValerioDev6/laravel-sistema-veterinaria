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
                        <li class="breadcrumb-item active">Detalle #{{ $record->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Entrada #{{ $record->id }} - {{ $record->paciente?->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Mascota:</strong> {{ $record->paciente?->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Veterinario:</strong> {{ $record->user?->username }}
                        </div>
                        <div class="col-md-6">
                            <strong>Evento:</strong> {{ ucfirst($record->event_type) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha:</strong> {{ $record->event_date?->format('d/m/Y') }}
                        </div>
                        @if ($record->cita)
                            <div class="col-md-6">
                                <strong>Cita asociada:</strong> #{{ $record->cita_id }}
                            </div>
                        @endif
                    </div>

                    <h6 class="text-muted mb-2">Notas</h6>
                    <p>{{ $record->notes ?: 'Sin notas registradas.' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Signos vitales</h4>
                </div>
                <div class="card-body">
                    @if ($record->vital_signs->isEmpty())
                        <p class="text-muted mb-0">No se registraron signos vitales.</p>
                    @else
                        <div class="row">
                            @foreach ($record->vital_signs as $vital)
                                <div class="col-md-3 mb-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold">
                                            {{ $vital->weight ?? '-' }} <small class="fs-6 text-muted">kg</small>
                                        </div>
                                        <div class="text-muted">Peso</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold">
                                            {{ $vital->temperature ?? '-' }} <small class="fs-6 text-muted">°C</small>
                                        </div>
                                        <div class="text-muted">Temperatura</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold">
                                            {{ $vital->heart_rate ?? '-' }} <small class="fs-6 text-muted">lpm</small>
                                        </div>
                                        <div class="text-muted">Frec. cardíaca</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-6">{{ $vital->recorded_at?->format('d/m/Y H:i') }}</div>
                                        <div class="text-muted">Registrado</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Prescripciones</h4>
                </div>
                <div class="card-body">
                    @if ($record->prescriptions->isEmpty())
                        <p class="text-muted mb-0">No se registraron prescripciones.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Dosis</th>
                                        <th>Duración (días)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($record->prescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription->medicine?->name }}</td>
                                            <td>{{ $prescription->dosage }}</td>
                                            <td>{{ $prescription->duration_days ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Adjuntos</h4>
                </div>
                <div class="card-body">
                    <form id="formSubirAdjunto" enctype="multipart/form-data">
                        <input type="hidden" name="medical_record_id" value="{{ $record->id }}">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="archivo" name="file" accept=".jpg,.jpeg,.png,.webp,.pdf">
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="ri-upload-2-line me-1"></i>Subir archivo
                        </button>
                    </form>

                    <hr>

                    <div id="listaAdjuntos">
                        @foreach ($record->medical_record_attachments as $attachment)
                            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                <a href="{{ $attachment->file_url }}" target="_blank" class="text-truncate me-2">
                                    <i class="ri-file-line me-1"></i>{{ basename($attachment->file_url) }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-adjunto"
                                        data-id="{{ $attachment->id }}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger" id="btnEliminarRegistro">
                            <i class="ri-delete-bin-line me-1"></i>Eliminar entrada
                        </button>
                        <a href="{{ route('admin.pacientes.show', $record->pet_id) }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i>Ver ficha de la mascota
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>window.registro = @json($record->id);</script>
        <script src="{{ asset('js/pages/medical-records.js') }}"></script>
    @endpush
</x-app-layout>