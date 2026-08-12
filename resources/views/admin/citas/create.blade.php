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
                        <li class="breadcrumb-item active">Nueva Cita</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0"><i class="ri-calendar-event-line me-1 text-primary"></i>Nueva Cita</h4>
                    <span class="badge bg-success-subtle text-success">Cita con pago</span>
                </div>
                <div class="card-body">
                    <form id="formCrearCita" class="row g-3">
                        {{-- 1. Fecha (disparador de disponibilidad) --}}
                        <div class="col-md-4">
                            <label class="form-label" for="appointment_date">Fecha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <div id="sinDisponibilidad" class="alert alert-warning mb-0 d-none">
                                No hay veterinarios disponibles para la fecha seleccionada.
                            </div>
                            <div id="bloqueDisponibilidad" class="d-none">
                                <label class="form-label">Veterinarios disponibles</label>
                                <div class="row g-2" id="contenedorDisponibilidad"></div>
                                <div class="d-none mt-2" id="bloqueHoras">
                                    <label class="form-label">Horas libres de <span id="nombreVetSeleccionado"></span></label>
                                    <div class="d-flex flex-wrap gap-1" id="contenedorHoras"></div>
                                </div>
                                <input type="hidden" name="veterinarian_id" id="veterinarian_id">
                                <input type="hidden" name="appointment_time" id="appointment_time">
                            </div>
                        </div>

                        {{-- 2. Mascota con preview --}}
                        <div class="col-md-6">
                            <label class="form-label" for="pet_id">Mascota</label>
                            <select class="form-select" id="pet_id" name="pet_id" required>
                                <option value="">Seleccionar mascota</option>
                                @foreach ($pacientes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- 3. Servicio (principal) --}}
                        <div class="col-md-6">
                            <label class="form-label" for="service_id">Servicio</label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Seleccionar servicio</option>
                                @foreach ($services as $service)
                                    <option
                                        value="{{ $service->id }}"
                                        data-price="{{ $service->base_price }}"
                                        data-duration="{{ $service->duration_minutes }}"
                                    >
                                        {{ $service->name }} — S/ {{ number_format($service->base_price, 2) }}
                                    </option>
                                @endforeach
                                <option value="__nuevo__">＋ Crear nuevo servicio</option>
                            </select>
                            <div class="invalid-feedback"></div>

                            <div id="bloqueNuevoServicio" class="mt-3 d-none">
                                <div class="border rounded-3 p-3 bg-light-subtle">
                                    <h6 class="card-title mb-2 text-primary">
                                        <i class="ri-add-circle-line me-1"></i>Nuevo servicio
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_service_name">Nombre</label>
                                            <input type="text" class="form-control" id="nuevo_service_name" name="new_service[name]" placeholder="Ej. Cortar uñas">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_service_category">Categoría</label>
                                            <select class="form-select" id="nuevo_service_category" name="new_service[category]">
                                                @foreach ($categories as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_service_price">Precio (S/)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="nuevo_service_price" name="new_service[base_price]" placeholder="0.00">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_service_duration">Duración (min)</label>
                                            <input type="number" min="5" max="600" class="form-control" id="nuevo_service_duration" name="new_service[duration_minutes]" value="30">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Preview mascota --}}
                        <div class="col-12 d-none" id="bloquePreviewMascota">
                            <div class="border rounded-3 p-2 bg-light-subtle d-flex align-items-center gap-3">
                                <div id="previewFotoMascota"></div>
                                <div id="previewDatosMascota" class="small text-muted"></div>
                            </div>
                        </div>

                        {{-- 4. Motivo --}}
                        <div class="col-md-8">
                            <label class="form-label" for="reason">Motivo</label>
                            <textarea class="form-control" id="reason" name="reason" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="reminder_date">Recordatorio <span class="text-muted">(opcional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-notification-3-line"></i></span>
                                <input type="date" class="form-control" id="reminder_date" name="reminder_date">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- 5. Pago (prioridad) --}}
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light-subtle">
                                <h6 class="mb-3">
                                    <i class="ri-bank-card-line me-1 text-success"></i>Pago
                                    <span class="text-muted small fw-normal">(obligatorio)</span>
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="total_pago">Total a pagar (S/)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
                                            <input type="text" class="form-control" id="total_pago" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="payment_method">Método de pago</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="">Seleccionar</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="advance_amount">Adelanto / pago (S/)</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="advance_amount" name="advance_amount" placeholder="0.00" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarCita">
                                <i class="ri-save-line me-1"></i>Guardar y cobrar
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
        <script>
            window.citasPacientesData = @json($pacientesData);
        </script>
    @endpush
</x-app-layout>
