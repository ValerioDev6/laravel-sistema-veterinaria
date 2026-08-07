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
                        <li class="breadcrumb-item active">Registrar Entrada</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Registrar Entrada de Historial</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearRegistro">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pet_id">Mascota</label>
                                <select class="form-select" id="pet_id" name="pet_id">
                                    <option value="">Seleccionar mascota</option>
                                    @foreach ($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}">{{ $paciente->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="veterinarian_id">Veterinario</label>
                                <select class="form-select" id="veterinarian_id" name="veterinarian_id">
                                    <option value="">Seleccionar veterinario</option>
                                    @foreach ($veterinarios as $veterinario)
                                        <option value="{{ $veterinario->id }}">{{ $veterinario->username }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="event_type">Tipo de evento</label>
                                <select class="form-select" id="event_type" name="event_type">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="consulta">Consulta</option>
                                    <option value="vacuna">Vacunación</option>
                                    <option value="cirugia">Cirugía</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="event_date">Fecha del evento</label>
                                <input type="date" class="form-control" id="event_date" name="event_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="cita_id">Cita <span class="text-muted">(opcional)</span></label>
                                <select class="form-select" id="cita_id" name="cita_id">
                                    <option value="">Sin cita asociada</option>
                                    @foreach ($citas as $cita)
                                        <option value="{{ $cita->id }}">
                                            #{{ $cita->id }} - {{ $cita->paciente?->name }} - {{ $cita->fecha?->format('Y-m-d') }} {{ $cita->hora }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="notes">Notas</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Observaciones de la atención"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="card bg-light-subtle mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Signos vitales <span class="text-muted fs-6">(opcional)</span></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vital_weight">Peso (kg)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="vital_weight" name="vital_signs[weight]">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vital_temperature">Temperatura (°C)</label>
                                        <input type="number" step="0.1" min="30" max="45" class="form-control" id="vital_temperature" name="vital_signs[temperature]">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vital_heart_rate">Frecuencia cardíaca (lpm)</label>
                                        <input type="number" min="0" max="500" class="form-control" id="vital_heart_rate" name="vital_signs[heart_rate]">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light-subtle mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Prescripciones <span class="text-muted fs-6">(opcional)</span></h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarReceta">
                                    <i class="ri-add-line me-1"></i>Agregar medicamento
                                </button>
                            </div>
                            <div class="card-body" id="listaRecetas"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarRegistro">
                                <i class="ri-save-line me-1"></i>Guardar
                            </button>
                            <a href="{{ route('admin.medical-records.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>window.medicines = @json($medicines);</script>
        <script src="{{ asset('js/pages/medical-records.js') }}"></script>
    @endpush
</x-app-layout>