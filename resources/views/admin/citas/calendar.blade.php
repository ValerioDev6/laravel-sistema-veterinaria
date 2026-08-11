<x-app-layout>
    @push('styles')
        <style>
            #calendar-citas .fc-toolbar-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #495057;
            }

            #calendar-citas .fc-button {
                border-radius: 0.5rem;
                border: 1px solid #e2e8f0;
                background: #fff;
                color: #495057;
                font-weight: 500;
                text-transform: capitalize;
                padding: 0.35rem 0.9rem;
                box-shadow: none;
                transition: all 0.15s ease-in-out;
            }

            #calendar-citas .fc-button.fc-button-active,
            #calendar-citas .fc-button:hover {
                background: #405189;
                border-color: #405189;
                color: #fff;
            }

            #calendar-citas .fc-button-primary:disabled {
                background: #f1f5f9;
                border-color: #e2e8f0;
                color: #94a3b8;
            }

            #calendar-citas .fc-daygrid-day,
            #calendar-citas .fc-timegrid-slot {
                border-color: #eef2f7;
            }

            #calendar-citas .fc-day-today {
                background: rgba(80, 165, 241, 0.08) !important;
            }

            #calendar-citas .fc-daygrid-day-number {
                font-size: 0.8rem;
                color: #6c757d;
                font-weight: 500;
            }

            #calendar-citas .fc-event {
                border-radius: 0.4rem;
                border: none;
                border-left: 4px solid rgba(0, 0, 0, 0.25);
                padding: 2px 6px;
                font-size: 0.78rem;
                font-weight: 500;
                cursor: pointer;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            }

            #calendar-citas .fc-event:hover {
                filter: brightness(1.1);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                transform: translateY(-1px);
            }

            #calendar-citas .fc-event-title {
                font-weight: 600;
            }

            #calendar-citas .fc-col-header-cell-cushion {
                font-size: 0.8rem;
                font-weight: 600;
                color: #495057;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            #calendar-citas .fc-timegrid-axis-cushion,
            #calendar-citas .fc-timegrid-slot-label-cushion {
                font-size: 0.75rem;
                color: #6c757d;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.citas.index') }}">Citas</a>
                        </li>
                        <li class="breadcrumb-item active">Calendario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Calendario de Citas</h4>
                </div>
                <div class="card-body">
                    <div id="calendar-citas"></div>
                    <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top" id="leyendaVets">
                        <span class="text-muted small fw-semibold text-uppercase">Veterinarios:</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer lateral derecho: detalle de la cita + edición de estado -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCita"
        aria-labelledby="offcanvasCitaLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasCitaLabel">Cita médica</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <h6 class="text-muted text-uppercase fs-12 mb-2">Información de la Cita</h6>
            <dl class="row mb-3">
                <dt class="col-5">Veterinario</dt>
                <dd class="col-7" id="oc-veterinario">—</dd>
                <dt class="col-5">Mascota</dt>
                <dd class="col-7" id="oc-pet">—</dd>
                <dt class="col-5">Hora de Atención</dt>
                <dd class="col-7" id="oc-hora">—</dd>
                <dt class="col-5">Día</dt>
                <dd class="col-7" id="oc-dia">—</dd>
                <dt class="col-5">Costo de la Cita</dt>
                <dd class="col-7" id="oc-costo">—</dd>
                <dt class="col-5">Servicio</dt>
                <dd class="col-7" id="oc-servicio">—</dd>
                <dt class="col-5">Razón de la cita</dt>
                <dd class="col-7" id="oc-razon">—</dd>
                <dt class="col-5">Notas Médicas</dt>
                <dd class="col-7" id="oc-notas">—</dd>
            </dl>

            <hr>

            <h6 class="text-muted text-uppercase fs-12 mb-2">Estado</h6>
            <select class="form-select mb-3" id="oc-estado">
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="completada">Completada</option>
                <option value="cancelada">Cancelada</option>
            </select>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary" id="oc-guardar">Guardar cambios</button>
                <a href="#" class="btn btn-soft-primary" id="oc-editar">Editar Cita</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/citas-calendar.js') }}"></script>
    @endpush
</x-app-layout>
