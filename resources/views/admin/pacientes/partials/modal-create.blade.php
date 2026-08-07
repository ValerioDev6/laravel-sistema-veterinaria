<div class="modal fade" id="modalCrearPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearPacienteTitle">Registrar Nueva Mascota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formModalCrearPaciente">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="modal-crear-owner">Propietario <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                <select class="form-select" id="modal-crear-owner" name="owner_id" required>
                                    <option value="">Seleccionar propietario</option>
                                    @foreach ($owners as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="modal-crear-nombre">Nombre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-paw-line"></i></span>
                                <input type="text" class="form-control" id="modal-crear-nombre" name="name" placeholder="Ej. Rocky" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-specie">Especie <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-leaf-line"></i></span>
                                <select class="form-select" id="modal-crear-specie" name="species_id" required>
                                    <option value="">Seleccionar especie</option>
                                    @foreach ($species as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-breed">Raza</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-shapes-line"></i></span>
                                <select class="form-select" id="modal-crear-breed" name="breed_id" disabled>
                                    <option value="">Primero elige la especie</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-sexo">Sexo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-focus-2-line"></i></span>
                                <select class="form-select" id="modal-crear-sexo" name="gender" required>
                                    <option value="desconocido">Desconocido</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-fecha">Fecha de nacimiento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                <input type="date" class="form-control" id="modal-crear-fecha" name="birth_date">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-color">Color</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-palette-line"></i></span>
                                <input type="text" class="form-control" id="modal-crear-color" name="color" placeholder="Ej. Marrón">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="modal-crear-peso">Peso (kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-scales-3-line"></i></span>
                                <input type="number" step="0.01" min="0" class="form-control" id="modal-crear-peso" name="weight" placeholder="0.00">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="modal-crear-photo">Foto</label>
                            <input type="file" class="form-control" id="modal-crear-photo" name="photo" accept="image/*">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modal-crear-notas">Notas médicas</label>
                            <textarea class="form-control" id="modal-crear-notas" name="medical_notes" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formModalCrearPaciente" class="btn btn-primary" id="btnModalCrearPaciente">
                    <i class="ri-save-line me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>