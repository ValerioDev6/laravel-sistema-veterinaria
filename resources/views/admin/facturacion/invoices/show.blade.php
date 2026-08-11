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
                        <li class="breadcrumb-item active">Detalle #{{ $invoice->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Factura #{{ $invoice->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Servicio:</strong> {{ ucfirst($invoice->invoiceable_type) }} #{{ $invoice->invoiceable_id }}
                        </div>
                        <div class="col-md-6">
                            <strong>Dueño:</strong> {{ $invoice->owner?->first_name }} {{ $invoice->owner?->last_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total:</strong> ${{ number_format($invoice->total, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Saldo:</strong> ${{ number_format($invoice->remaining_balance, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            <span class="badge bg-{{ $invoice->status === 'pagado' ? 'success' : ($invoice->status === 'anulado' ? 'secondary' : ($invoice->status === 'parcial' ? 'warning' : 'info')) }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Emitido:</strong> {{ $invoice->issued_at?->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Pagos</h4>
                </div>
                <div class="card-body">
                    @if ($invoice->payments->isEmpty())
                        <p class="text-muted mb-0">No se registraron pagos.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless dt-responsive nowrap w-100">
                                <thead>
                                    <tr style="border-bottom: 2px solid #212529;">
                                        <th>ID</th>
                                        <th>Monto</th>
                                        <th>Anticipo</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaPagos">
                                    @foreach ($invoice->payments as $payment)
                                        <tr>
                                            <td>#{{ $payment->id }}</td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->advance_amount ? '$' . number_format($payment->advance_amount, 2) : '-' }}</td>
                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'anulado' ? 'secondary' : 'success' }}">
                                                    {{ ucfirst($payment->status ?? 'pagado') }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($payment->status !== 'anulado')
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-anular-pago"
                                                            data-id="{{ $payment->id }}">
                                                        <i class="ri-close-circle-line"></i>
                                                    </button>
                                                @endif
                                            </td>
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
            @if ($invoice->status !== 'anulado' && $invoice->remaining_balance > 0)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Registrar pago</h4>
                    </div>
                    <div class="card-body">
                        <form id="formRegistrarPago">
                            <div class="mb-3">
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="amount"
                                       placeholder="Monto a cobrar">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Anticipo (opcional)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="advance_amount"
                                       placeholder="Anticipo">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Método de pago</label>
                                <select class="form-select" name="payment_method">
                                    <option value="">Seleccione</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fecha de pago</label>
                                <input type="date" class="form-control" name="paid_at">
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="btnRegistrarPago">
                                <i class="ri-bank-card-line me-1"></i>Registrar pago
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($invoice->status !== 'anulado')
                            <button type="button" class="btn btn-outline-danger" id="btnAnularFactura">
                                <i class="ri-close-circle-line me-1"></i>Anular factura
                            </button>
                        @endif
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i>Volver a facturas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>window.factura = @json($invoice->id);</script>
        <script src="{{ asset('js/pages/invoices.js') }}"></script>
    @endpush
</x-app-layout>