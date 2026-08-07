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
                        <li class="breadcrumb-item active">Registrar Vacuna</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Registrar Vacuna</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearVacuna">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pet_id">Mascota</label>
                                <select class="form-select" id="pet_id" name="pet_id">
                                    <option value="">Seleccionar mascota</option>
                                    @foreach ($pacientes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="vaccine_type_id">Tipo de vacuna</label>
                                <select class="form-select" id="vaccine_type_id" name="vaccine_type_id">
                                    <option value="">Seleccionar tipo</option>
                                    @foreach ($vaccineTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="veterinarian_id">Veterinario</label>
                                <select class="form-select" id="veterinarian_id" name="veterinarian_id">
                                    <option value="">Seleccionar veterinario</option>
                                    @foreach ($veterinarians as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="cita_id">Cita <span class="text-muted">(opcional)</span></label>
                                <select class="form-select" id="cita_id" name="cita_id">
                                    <option value="">Sin cita asociada</option>
                                    @foreach ($citas as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="vaccination_date">Fecha de vacunación</label>
                                <input type="date" class="form-control" id="vaccination_date" name="vaccination_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="next_due_date">Próxima dosis <span class="text-muted">(opcional)</span></label>
                                <input type="date" class="form-control" id="next_due_date" name="next_due_date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarVacuna">
                                <i class="ri-save-line me-1"></i>Guardar
                            </button>
                            <a href="{{ route('admin.vacunas.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/vacunas.js') }}"></script>
    @endpush
</x-app-layout>