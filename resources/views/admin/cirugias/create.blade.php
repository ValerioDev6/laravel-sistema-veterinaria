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
                        <li class="breadcrumb-item active">Registrar Cirugía</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0"><i class="ri-scissors-line me-1 text-primary"></i>Registrar Cirugía</h4>
                    <span class="badge bg-success-subtle text-success">Cirugía con pago</span>
                </div>
                <div class="card-body">
                    <form id="formCrearCirugia" class="row g-3">
                        {{-- 1. Fecha (disparador de disponibilidad) --}}
                        <div class="col-md-4">
                            <label class="form-label" for="surgery_date">Fecha de la cirugía</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                <input type="date" class="form-control" id="surgery_date" name="surgery_date"
                                    min="{{ now()->toDateString() }}" required>
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
                                    <label class="form-label">Horas libres de <span
                                            id="nombreVetSeleccionado"></span></label>
                                    <div class="d-flex flex-wrap gap-1" id="contenedorHoras"></div>
                                </div>
                                <input type="hidden" name="veterinarian_id" id="veterinarian_id">
                                <input type="hidden" name="surgery_time" id="surgery_time">
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

                        {{-- Preview mascota --}}
                        <div class="col-12 d-none" id="bloquePreviewMascota">
                            <div class="border rounded-3 p-2 bg-light-subtle d-flex align-items-center gap-3">
                                <div id="previewFotoMascota"></div>
                                <div id="previewDatosMascota" class="small text-muted"></div>
                            </div>
                        </div>

                        {{-- 3. Tipo de cirugía --}}
                        <div class="col-md-6">
                            <label class="form-label" for="surgery_type">Tipo de cirugía</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-scissors-2-line"></i></span>
                                <input type="text" class="form-control" id="surgery_type" name="surgery_type"
                                    placeholder="Ej. Esterilización">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- 4. Cita (opcional) --}}
                        <div class="col-md-6">
                            <label class="form-label" for="cita_id">Cita <span
                                    class="text-muted">(opcional)</span></label>
                            <select class="form-select" id="cita_id" name="cita_id">
                                <option value="">Sin cita asociada</option>
                                @foreach ($citas as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>


                        {{-- 6. Resultado y notas --}}
                        <div class="col-md-6">
                            <label class="form-label" for="outcome">Resultado</label>
                            <textarea class="form-control" id="outcome"s name="outcome" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="medical_notes">Notas médicas</label>
                            <textarea class="form-control" id="medical_notes" name="medical_notes" rows="2"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- 7. Pago (obligatorio) --}}
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light-subtle">
                                <h6 class="mb-3">
                                    <i class="ri-bank-card-line me-1 text-success"></i>Pago
                                    <span class="text-muted small fw-normal">(obligatorio)</span>
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="total_pago">Total de la cirugía (S/)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="ri-money-dollar-circle-line"></i></span>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="total_pago" name="total" placeholder="0.00" value="0.00"
                                                required>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="payment_method">Método de pago</label>
                                        <select class="form-select" id="payment_method" name="payment_method"
                                            required>
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
                                        <input type="number" step="0.01" min="0.01" class="form-control"
                                            id="advance_amount" name="advance_amount" placeholder="0.00" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarCirugia">
                                <i class="ri-save-line me-1"></i>Guardar y cobrar
                            </button>
                            <a href="{{ route('admin.cirugias.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.cirugiasPacientesData = @json($pacientesData);
        </script>
        <script src="{{ asset('js/pages/cirugias.js') }}"></script>
    @endpush
</x-app-layout>
