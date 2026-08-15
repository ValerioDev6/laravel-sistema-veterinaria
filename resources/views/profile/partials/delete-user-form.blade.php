<section>
    <p class="text-muted mb-4">
        {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Antes de eliminarla, descarga cualquier información que desees conservar.') }}
    </p>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
        <i class="ri-delete-bin-line me-1"></i>{{ __('Eliminar cuenta') }}
    </button>

    <div class="modal fade" id="modalEliminarCuenta" tabindex="-1" aria-labelledby="modalEliminarCuentaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarCuentaLabel">{{ __('¿Seguro que quieres eliminar tu cuenta?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Escribe tu contraseña para confirmar la eliminación.') }}
                        </p>

                        <label class="form-label" for="password">{{ __('Contraseña') }}</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            placeholder="{{ __('Contraseña') }}"
                            autocomplete="current-password"
                        />
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line me-1"></i>{{ __('Eliminar cuenta') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
