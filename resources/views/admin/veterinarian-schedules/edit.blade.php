<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.veterinarian-schedules.index') }}">Horarios</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Horario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Horario</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarSchedule" data-id="{{ $schedule->id }}">
                        <div class="mb-3">
                            <label class="form-label" for="veterinarian_id">Veterinario</label>
                            <select class="form-select" id="veterinarian_id" name="veterinarian_id">
                                <option value="">Seleccionar veterinario</option>
                                @foreach ($veterinarians as $value => $label)
                                    <option value="{{ $value }}" @selected($schedule->veterinarian_id == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="day_of_week">Día</label>
                            <select class="form-select" id="day_of_week" name="day_of_week">
                                <option value="">Seleccionar día</option>
                                @foreach ($days as $value => $label)
                                    <option value="{{ $value }}" @selected($schedule->day_of_week == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="start_time">Hora inicio</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" value="{{ $schedule->start_time?->format('H:i') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="end_time">Hora fin</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" value="{{ $schedule->end_time?->format('H:i') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($schedule->is_active)>
                            <label class="form-check-label" for="is_active">Activo</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarSchedule">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.veterinarian-schedules.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/veterinarian-schedules.js') }}"></script>
    @endpush
</x-app-layout>