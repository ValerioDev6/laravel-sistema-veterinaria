<form id="formCrearPaciente">
    <input type="hidden" id="owner_id" name="owner_id" value="">
    <div class="row g-3">
        {{-- Sección: Propietario --}}
        <div class="col-12">
            <h6 class="text-uppercase text-muted fw-semibold mb-2">
                <i class="ri-user-2-line me-1"></i>Información del propietario <span class="text-danger">*</span>
            </h6>
            <hr class="mt-0">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="first_name">Nombres <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-user-line"></i></span>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ej. Juan Carlos" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="last_name">Apellidos <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-user-line"></i></span>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ej. Pérez Gómez" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="type_documento">Tipo de documento</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-credit-card-line"></i></span>
                <select class="form-select" id="type_documento" name="type_documento">
                    <option value="">Seleccionar</option>
                    <option value="DNI">DNI</option>
                    <option value="CE">CE</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="n_documento">N° de documento</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-hashtag"></i></span>
                <input type="text" class="form-control" id="n_documento" name="n_documento" placeholder="Ej. 70123456">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">Teléfono <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-phone-line"></i></span>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Ej. 999 888 777" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="email">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="usuario@correo.com">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="address">Dirección</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-map-pin-line"></i></span>
                <input type="text" class="form-control" id="address" name="address" placeholder="Ej. Av. Los Laureles 123">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="city">Ciudad</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-building-line"></i></span>
                <input type="text" class="form-control" id="city" name="city" placeholder="Ej. Lima">
            </div>
            <div class="invalid-feedback"></div>
        </div>

        {{-- Sección: Mascota --}}
        <div class="col-12">
            <h6 class="text-uppercase text-muted fw-semibold mb-2">
                <i class="ri-paw-print-line me-1"></i>Información de la mascota <span class="text-danger">*</span>
            </h6>
            <hr class="mt-0">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-paw-line"></i></span>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Rocky" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
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
        <div class="col-md-6">
            <label class="form-label" for="breed_id">Raza</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-shapes-line"></i></span>
                <select class="form-select" id="breed_id" name="breed_id" disabled>
                    <option value="">Primero elige la especie</option>
                </select>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
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
        <div class="col-md-6">
            <label class="form-label" for="birth_date">Fecha de nacimiento</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                <input type="date" class="form-control" id="birth_date" name="birth_date">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="color">Color</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-palette-line"></i></span>
                <input type="text" class="form-control" id="color" name="color" placeholder="Ej. Marrón">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="weight">Peso (kg)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ri-scales-3-line"></i></span>
                <input type="number" step="0.01" min="0" class="form-control" id="weight" name="weight" placeholder="0.00">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="photo">Foto</label>
            <div class="dropzone" id="dropzoneFoto">
                <div class="dz-message needsclick">
                    <i class="ri-image-add-line fs-2 text-muted"></i>
                    <span class="text-muted">Arrastra la foto aquí o haz clic para seleccionar</span>
                </div>
            </div>
            <div id="previewFotoActual" class="mt-2 d-none">
                <span class="text-muted d-block" style="font-size: 12px;">Foto actual</span>
                <img src="" alt="Foto actual" class="rounded mt-1" style="max-height: 80px;">
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-12">
            <label class="form-label" for="medical_notes">Notas médicas</label>
            <textarea class="form-control" id="medical_notes" name="medical_notes" rows="3"></textarea>
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
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