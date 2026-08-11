<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.vacunas.index') }}">Vacunas</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Vacuna #{{ $vacuna->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0"><i class="ri-virus-line me-1 text-primary"></i>Editar Vacuna #{{ $vacuna->id }}</h4>
                    <span class="badge bg-success-subtle text-success">Vacunación con pago</span>
                </div>
                <div class="card-body">
                    <form id="formEditarVacuna" data-id="{{ $vacuna->id }}" class="row g-3">
                        {{-- 1. Fecha (disparador de disponibilidad) --}}
                        <div class="col-md-4">
                            <label class="form-label" for="vaccination_date">Fecha de vacunación</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                <input type="date" class="form-control" id="vaccination_date" name="vaccination_date" min="{{ $vacuna->vaccination_date && $vacuna->vaccination_date->isPast() ? $vacuna->vaccination_date->format('Y-m-d') : now()->toDateString() }}" value="{{ $vacuna->vaccination_date?->format('Y-m-d') }}" required>
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
                                <input type="hidden" name="veterinarian_id" id="veterinarian_id" value="{{ $vacuna->veterinarian_id }}">
                                <input type="hidden" name="vaccination_time" id="vaccination_time" value="{{ $vacuna->vaccination_time?->format('H:i') }}">
                            </div>
                        </div>

                        {{-- 2. Mascota con preview --}}
                        <div class="col-md-6">
                            <label class="form-label" for="pet_id">Mascota</label>
                            <select class="form-select" id="pet_id" name="pet_id" required>
                                <option value="">Seleccionar mascota</option>
                                @foreach ($pacientes as $value => $label)
                                    <option value="{{ $value }}" @selected($vacuna->pet_id == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- 3. Tipo de vacuna --}}
                        <div class="col-md-6">
                            <label class="form-label" for="vaccine_type_id">Tipo de vacuna</label>
                            <select class="form-select" id="vaccine_type_id" name="vaccine_type_id" required>
                                <option value="">Seleccionar tipo</option>
                                @foreach ($vaccineTypes as $v)
                                    <option
                                        value="{{ $v->id }}"
                                        data-price="{{ $v->base_price }}"
                                        data-species="{{ $v->species_id ?? '' }}"
                                        @selected($vacuna->vaccine_type_id == $v->id)
                                    >
                                        {{ $v->name }} — S/ {{ number_format($v->base_price, 2) }}{{ $v->species ? ' (' . $v->species->name . ')' : '' }}
                                    </option>
                                @endforeach
                                <option value="__nuevo__">＋ Crear nuevo tipo de vacuna</option>
                            </select>
                            <div class="invalid-feedback"></div>

                            <div id="bloqueNuevoTipoVacuna" class="mt-3 d-none">
                                <div class="border rounded-3 p-3 bg-light-subtle">
                                    <h6 class="card-title mb-2 text-primary">
                                        <i class="ri-add-circle-line me-1"></i>Nuevo tipo de vacuna
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_tipo_vacuna_name">Nombre</label>
                                            <input type="text" class="form-control" id="nuevo_tipo_vacuna_name" name="new_vaccine_type[name]" placeholder="Ej. Parvovirus canino">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_tipo_vacuna_price">Precio (S/)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="nuevo_tipo_vacuna_price" name="new_vaccine_type[base_price]" placeholder="0.00">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="nuevo_tipo_vacuna_species">Especie <span class="text-muted">(opcional)</span></label>
                                            <select class="form-select" id="nuevo_tipo_vacuna_species" name="new_vaccine_type[species_id]">
                                                <option value="">Sin especie</option>
                                                @foreach ($species as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
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

                        {{-- 4. Próxima dosis --}}
                        <div class="col-md-6">
                            <label class="form-label" for="next_due_date">Próxima dosis <span class="text-muted">(opcional)</span></label>
                            <input type="date" class="form-control" id="next_due_date" name="next_due_date" value="{{ $vacuna->next_due_date?->format('Y-m-d') }}">
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
                                            <input type="text" class="form-control" id="total_pago" value="{{ number_format($invoice?->total ?? $vacuna->vaccine_type?->base_price ?? 0, 2, '.', '') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="payment_method">Método de pago</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="">Seleccionar</option>
                                            <option value="efectivo" @selected($invoice?->payments->first()?->payment_method == 'efectivo')>Efectivo</option>
                                            <option value="tarjeta" @selected($invoice?->payments->first()?->payment_method == 'tarjeta')>Tarjeta</option>
                                            <option value="transferencia" @selected($invoice?->payments->first()?->payment_method == 'transferencia')>Transferencia</option>
                                            <option value="otro" @selected($invoice?->payments->first()?->payment_method == 'otro')>Otro</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="advance_amount">Adelanto / pago (S/)</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="advance_amount" name="advance_amount" placeholder="0.00" value="{{ $invoice?->payments->first()?->amount }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarVacuna">
                                <i class="ri-save-line me-1"></i>Actualizar y cobrar
                            </button>
                            <a href="{{ route('admin.vacunas.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.vacunasPacientesData = @json($pacientesData);
            window.vacunaEditarInit = {
                fecha: {{ \Illuminate\Support\Js::from($vacuna->vaccination_date?->format('Y-m-d')) }},
                veterinario: {{ $vacuna->veterinarian_id }},
                hora: {{ \Illuminate\Support\Js::from($vacuna->vaccination_time?->format('H:i')) }}
            };
        </script>
        <script src="{{ asset('js/pages/vacunas.js') }}"></script>
    @endpush
</x-app-layout>