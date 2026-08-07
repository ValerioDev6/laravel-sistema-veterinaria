<div class="modal fade" id="modalSpecies" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSpeciesTitle">Nueva Especie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formModalSpecies">
                    <input type="hidden" name="id" id="modal-species-id">
                    <div class="mb-3">
                        <label class="form-label" for="modal-species-nombre">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-leaf-line"></i></span>
                            <input type="text" class="form-control" id="modal-species-nombre" name="name" placeholder="Ej. Perro, Gato, Ave…" required>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formModalSpecies" class="btn btn-primary" id="btnModalSpeciesGuardar">
                    <i class="ri-save-line me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>