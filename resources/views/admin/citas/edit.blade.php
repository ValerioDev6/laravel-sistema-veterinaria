<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.citas.index') }}">Citas</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Cita</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Cita</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarCita" data-id="{{ $cita->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pet_id">Mascota</label>
                                <select class="form-select" id="pet_id" name="pet_id">
                                    <option value="">Seleccionar mascota</option>
                                    @foreach ($pacientes as $value => $label)
                                        <option value="{{ $value }}" @selected($cita->pet_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="veterinarian_id">Veterinario</label>
                                <select class="form-select" id="veterinarian_id" name="veterinarian_id">
                                    <option value="">Seleccionar veterinario</option>
                                    @foreach ($veterinarians as $value => $label)
                                        <option value="{{ $value }}" @selected($cita->veterinarian_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="service_id">Servicio <span class="text-muted">(opcional)</span></label>
                                <select class="form-select" id="service_id" name="service_id">
                                    <option value="">Sin servicio</option>
                                    @foreach ($services as $value => $label)
                                        <option value="{{ $value }}" @selected($cita->service_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pendiente" @selected($cita->status == 'pendiente')>Pendiente</option>
                                    <option value="confirmada" @selected($cita->status == 'confirmada')>Confirmada</option>
                                    <option value="completada" @selected($cita->status == 'completada')>Completada</option>
                                    <option value="cancelada" @selected($cita->status == 'cancelada')>Cancelada</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="appointment_date">Fecha</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="{{ $cita->appointment_date?->format('Y-m-d') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="appointment_time">Hora</label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="{{ $cita->appointment_time?->format('H:i') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="reason">Motivo</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3">{{ $cita->reason }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarCita">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.citas.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/citas.js') }}"></script>
    @endpush
</x-app-layout>