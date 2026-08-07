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
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-invoices" role="tab" data-tab="invoices">
                                <i class="ri-file-list-3-line me-1"></i>Facturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-payments" role="tab" data-tab="payments">
                                <i class="ri-bank-card-line me-1"></i>Pagos
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-invoices" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Facturas</h4>
                                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line me-1"></i>Nueva factura
                                </a>
                            </div>
                            <form id="formFiltros" class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <select class="form-select" id="filtro_status" name="status">
                                        <option value="">Todos los estados</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="parcial">Parcial</option>
                                        <option value="pagado">Pagado</option>
                                        <option value="anulado">Anulado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filtro_owner_id" name="owner_id">
                                        <option value="">Todos los dueños</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->first_name }} {{ $owner->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" name="desde" placeholder="Desde">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" name="hasta" placeholder="Hasta">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table id="table-invoices" class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Servicio</th>
                                            <th>Posadero / Dueño</th>
                                            <th>Total</th>
                                            <th>Saldo</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Pagos</h4>
                            </div>
                            <form id="formFiltrosPagos" class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <select class="form-select" id="filtro_status_pago" name="status">
                                        <option value="">Todos los estados</option>
                                        <option value="pagado">Pagado</option>
                                        <option value="anulado">Anulado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table id="table-payments" class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Factura</th>
                                            <th>Dueño</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/invoices.js') }}"></script>
    @endpush
</x-app-layout>