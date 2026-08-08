<form id="formOwner">
    <div class="row g-3">
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
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary" id="btnGuardarOwner">
            <i class="ri-save-line me-1"></i>Guardar
        </button>
        <button type="reset" class="btn btn-light">
            <i class="ri-refresh-line me-1"></i>Limpiar
        </button>
        <button type="button" class="btn btn-light" id="btnRegresarListado">
            <i class="ri-arrow-left-line me-1"></i>Regresar
        </button>
    </div>
</form>