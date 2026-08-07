<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.pacientes.index') }}">Pacientes</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Mascota</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Nueva Mascota</h4>
                </div>
                <div class="card-body">
                    <form id="formCrearPaciente">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="owner_id">Propietario</label>
                                <select class="form-select" id="owner_id" name="owner_id">
                                    <option value="">Seleccionar propietario</option>
                                    @foreach ($owners as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Rocky">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="species_id">Especie</label>
                                <select class="form-select" id="species_id" name="species_id">
                                    <option value="">Seleccionar especie</option>
                                    @foreach ($species as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="breed_id">Raza</label>
                                <select class="form-select" id="breed_id" name="breed_id" disabled>
                                    <option value="">Primero elige la especie</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="gender">Sexo</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="desconocido">Desconocido</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="birth_date">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="color">Color</label>
                                <input type="text" class="form-control" id="color" name="color" placeholder="Ej. Marrón">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="weight">Peso (kg)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="weight" name="weight" placeholder="0.00">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="photo">Foto</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="medical_notes">Notas médicas</label>
                                <textarea class="form-control" id="medical_notes" name="medical_notes" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnGuardarPaciente">
                                <i class="ri-save-line me-1"></i>Guardar
                            </button>
                            <a href="{{ route('admin.pacientes.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/pacientes.js') }}"></script>
    @endpush
</x-app-layout>