<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.cirugias.index') }}">Cirugías</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Cirugía</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Cirugía</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarCirugia" data-id="{{ $cirugia->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pet_id">Mascota</label>
                                <select class="form-select" id="pet_id" name="pet_id">
                                    <option value="">Seleccionar mascota</option>
                                    @foreach ($pacientes as $value => $label)
                                        <option value="{{ $value }}" @selected($cirugia->pet_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="veterinarian_id">Veterinario</label>
                                <select class="form-select" id="veterinarian_id" name="veterinarian_id">
                                    <option value="">Seleccionar veterinario</option>
                                    @foreach ($veterinarians as $value => $label)
                                        <option value="{{ $value }}" @selected($cirugia->veterinarian_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="surgery_type">Tipo de cirugía</label>
                                <input type="text" class="form-control" id="surgery_type" name="surgery_type" value="{{ $cirugia->surgery_type }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="cita_id">Cita <span class="text-muted">(opcional)</span></label>
                                <select class="form-select" id="cita_id" name="cita_id">
                                    <option value="">Sin cita asociada</option>
                                    @foreach ($citas as $value => $label)
                                        <option value="{{ $value }}" @selected($cirugia->cita_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="surgery_date">Fecha y hora</label>
                                <input type="datetime-local" class="form-control" id="surgery_date" name="surgery_date" value="{{ $cirugia->surgery_date?->format('Y-m-d\TH:i') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pendiente" @selected($cirugia->status == 'pendiente')>Pendiente</option>
                                    <option value="en_proceso" @selected($cirugia->status == 'en_proceso')>En proceso</option>
                                    <option value="completada" @selected($cirugia->status == 'completada')>Completada</option>
                                    <option value="cancelada" @selected($cirugia->status == 'cancelada')>Cancelada</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="outcome">Resultado</label>
                                <textarea class="form-control" id="outcome" name="outcome" rows="2">{{ $cirugia->outcome }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="medical_notes">Notas médicas</label>
                                <textarea class="form-control" id="medical_notes" name="medical_notes" rows="3">{{ $cirugia->medical_notes }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarCirugia">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.cirugias.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/cirugias.js') }}"></script>
    @endpush
</x-app-layout>