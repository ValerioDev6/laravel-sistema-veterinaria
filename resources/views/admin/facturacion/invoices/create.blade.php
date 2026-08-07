<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.invoices.index') }}">Facturación</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Factura</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Generar Factura</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearFactura">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="owner_id">Dueño</label>
                                <select class="form-select" id="owner_id" name="owner_id">
                                    <option value="">Seleccionar dueño</option>
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->first_name }} {{ $owner->last_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="invoiceable_type">Tipo de servicio</label>
                                <select class="form-select" id="invoiceable_type" name="invoiceable_type">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="cita">Cita</option>
                                    <option value="vacuna">Vacuna</option>
                                    <option value="surgiere">Cirugía</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="invoiceable_id">Servicio</label>
                                <select class="form-select" id="invoiceable_id" name="invoiceable_id">
                                    <option value="">Primero seleccione el tipo</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="issued_at">Fecha de emisión</label>
                                <input type="date" class="form-control" id="issued_at" name="issued_at">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="total">Total</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="total" name="total" placeholder="Monto total">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3">Pago inicial</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="first_payment">¿Registrar primer pago?</label>
                                <select class="form-select" id="first_payment" name="first_payment">
                                    <option value="0">No</option>
                                    <option value="on">Sí</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="payment_method">Método de pago</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Seleccionar método</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="advance_amount">Anticipo</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="advance_amount" name="advance_amount" placeholder="Anticipo (opcional)">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarFactura">
                                <i class="ri-save-line me-1"></i>Generar factura
                            </button>
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.servicios = {
                cita: @json($citas),
                vacuna: @json($vacunas),
                surgiere: @json($cirugias),
            };
        </script>
        <script src="{{ asset('js/pages/invoices.js') }}"></script>
    @endpush
</x-app-layout>