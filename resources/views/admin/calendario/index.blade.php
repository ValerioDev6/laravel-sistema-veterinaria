<x-app-layout>
    @push('styles')
        <style>
            #calendar-general .fc-toolbar-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #495057;
            }

            #calendar-general .fc-button {
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

            #calendar-general .fc-button.fc-button-active,
            #calendar-general .fc-button:hover {
                background: #405189;
                border-color: #405189;
                color: #fff;
            }

            #calendar-general .fc-button-primary:disabled {
                background: #f1f5f9;
                border-color: #e2e8f0;
                color: #94a3b8;
            }

            #calendar-general .fc-daygrid-day,
            #calendar-general .fc-timegrid-slot {
                border-color: #eef2f7;
            }

            #calendar-general .fc-day-today {
                background: rgba(80, 165, 241, 0.08) !important;
            }

            #calendar-general .fc-daygrid-day-number {
                font-size: 0.8rem;
                color: #6c757d;
                font-weight: 500;
            }

            #calendar-general .fc-event {
                border-radius: 0.4rem;
                border: none;
                border-left: 4px solid rgba(0, 0, 0, 0.25);
                padding: 2px 6px;
                font-size: 0.78rem;
                font-weight: 500;
                cursor: pointer;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            }

            #calendar-general .fc-event:hover {
                filter: brightness(1.1);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                transform: translateY(-1px);
            }

            #calendar-general .fc-event-title {
                font-weight: 600;
            }

            #calendar-general .fc-event.ev-cita {
                background-color: #405189 !important;
                border-color: #405189 !important;
            }

            #calendar-general .fc-event.ev-vacuna {
                background-color: #10b981 !important;
                border-color: #10b981 !important;
            }

            #calendar-general .fc-event.ev-cirugia {
                background-color: #f59e0b !important;
                border-color: #f59e0b !important;
            }

            #calendar-general .fc-event.ev-cita .fc-event-title,
            #calendar-general .fc-event.ev-vacuna .fc-event-title,
            #calendar-general .fc-event.ev-cirugia .fc-event-title {
                color: #fff !important;
            }

            #calendar-general .fc-col-header-cell-cushion {
                font-size: 0.8rem;
                font-weight: 600;
                color: #495057;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            #calendar-general .fc-timegrid-axis-cushion,
            #calendar-general .fc-timegrid-slot-label-cushion {
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
                    <h4 class="card-title mb-0">Citas, Vacunas y Cirugías</h4>
                </div>
                <div class="card-body">
                    <div id="calendar-general"></div>
                    <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top" id="leyendaTipos">
                        <span class="text-muted small fw-semibold text-uppercase">Tipos:</span>
                        <span class="chip-tipo d-inline-flex align-items-center gap-1 small">
                            <span class="d-inline-block" style="width:12px;height:12px;border-radius:3px;background:#405189"></span>
                            Cita
                        </span>
                        <span class="chip-tipo d-inline-flex align-items-center gap-1 small">
                            <span class="d-inline-block" style="width:12px;height:12px;border-radius:3px;background:#10b981"></span>
                            Vacuna
                        </span>
                        <span class="chip-tipo d-inline-flex align-items-center gap-1 small">
                            <span class="d-inline-block" style="width:12px;height:12px;border-radius:3px;background:#f59e0b"></span>
                            Cirugía
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer lateral derecho: detalle del evento -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEvento"
        aria-labelledby="offcanvasEventoLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasEventoLabel">Detalle</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <span class="badge mb-3" id="oe-tipo-badge"></span>
            <h6 class="text-muted text-uppercase fs-12 mb-2">Información</h6>
            <dl class="row mb-3">
                <dt class="col-5">Mascota</dt>
                <dd class="col-7" id="oe-mascota">—</dd>
                <dt class="col-5">Especie</dt>
                <dd class="col-7" id="oe-especie">—</dd>
                <dt class="col-5">Raza</dt>
                <dd class="col-7" id="oe-raza">—</dd>
                <dt class="col-5">Dueño</dt>
                <dd class="col-7" id="oe-dueno">—</dd>
                <dt class="col-5">Teléfono</dt>
                <dd class="col-7" id="oe-telefono">—</dd>
                <dt class="col-5">Veterinario</dt>
                <dd class="col-7" id="oe-veterinario">—</dd>
                <dt class="col-5">Fecha y hora</dt>
                <dd class="col-7" id="oe-fecha">—</dd>
                <dt class="col-5" id="oe-detalle-label">Detalle</dt>
                <dd class="col-7" id="oe-detalle">—</dd>
                <dt class="col-5 d-none" id="oe-proxima-label">Próxima dosis</dt>
                <dd class="col-7 d-none" id="oe-proxima">—</dd>
                <dt class="col-5">Notas</dt>
                <dd class="col-7" id="oe-notas">—</dd>
            </dl>

            <hr>

            <h6 class="text-muted text-uppercase fs-12 mb-2">Estado</h6>
            <select class="form-select mb-3" id="oe-estado-select"></select>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary" id="oe-guardar">Guardar</button>
                <button type="button" class="btn btn-soft-danger" id="oe-cancelar">Cancelar</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/calendario.js') }}"></script>
    @endpush
</x-app-layout>
