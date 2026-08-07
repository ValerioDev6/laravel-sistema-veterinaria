<div class="modal fade" id="modalBreed" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBreedTitle">Nueva Raza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formModalBreed">
                    <input type="hidden" name="id" id="modal-breed-id">
                    <div class="mb-3">
                        <label class="form-label" for="modal-breed-species">Especie <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-leaf-line"></i></span>
                            <select class="form-select" id="modal-breed-species" name="species_id" required>
                                <option value="">Seleccionar especie</option>
                                @foreach ($species as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="modal-breed-nombre">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-shapes-line"></i></span>
                            <input type="text" class="form-control" id="modal-breed-nombre" name="name" placeholder="Ej. Labrador, Siamés…" required>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formModalBreed" class="btn btn-primary" id="btnModalBreedGuardar">
                    <i class="ri-save-line me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>