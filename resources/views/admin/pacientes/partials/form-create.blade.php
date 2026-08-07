<form id="formCrearPaciente">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="owner_id">Propietario <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-user-line"></i></span>
                <select class="form-select" id="owner_id" name="owner_id" required>
                    <option value="">Seleccionar propietario</option>
                    @foreach ($owners as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-paw-line"></i></span>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Rocky" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="species_id">Especie <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-leaf-line"></i></span>
                <select class="form-select" id="species_id" name="species_id" required>
                    <option value="">Seleccionar especie</option>
                    @foreach ($species as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="breed_id">Raza</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-shapes-line"></i></span>
                <select class="form-select" id="breed_id" name="breed_id" disabled>
                    <option value="">Primero elige la especie</option>
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="gender">Sexo <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-focus-2-line"></i></span>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="desconocido">Desconocido</option>
                    <option value="macho">Macho</option>
                    <option value="hembra">Hembra</option>
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="birth_date">Fecha de nacimiento</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                <input type="date" class="form-control" id="birth_date" name="birth_date">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="color">Color</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-palette-line"></i></span>
                <input type="text" class="form-control" id="color" name="color" placeholder="Ej. Marrón">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="weight">Peso (kg)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-scales-3-line"></i></span>
                <input type="number" step="0.01" min="0" class="form-control" id="weight" name="weight" placeholder="0.00">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="photo">Foto</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-12">
            <label class="form-label" for="medical_notes">Notas médicas</label>
            <textarea class="form-control" id="medical_notes" name="medical_notes" rows="3"></textarea>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary" id="btnGuardarPaciente">
            <i class="ri-save-line me-1"></i>Guardar
        </button>
        <button type="reset" class="btn btn-light">
            <i class="ri-refresh-line me-1"></i>Limpiar
        </button>
        @if (!empty($regresarUrl))
            <a href="{{ $regresarUrl }}" class="btn btn-light" @if (!empty($regresarTab)) data-bs-toggle="tab" @endif>
                <i class="ri-arrow-left-line me-1"></i>Regresar
            </a>
        @endif
    </div>
</form>
