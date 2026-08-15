<section>
    <p class="text-muted mb-4">
        {{ __('Asegúrate de usar una contraseña larga y aleatoria para mantener segura tu cuenta.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="row g-3">
        @csrf
        @method('put')

        <div class="col-12">
            <label class="form-label" for="update_password_current_password">{{ __('Contraseña actual') }}</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                autocomplete="current-password"
            />
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="update_password_password">{{ __('Nueva contraseña') }}</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password"
            />
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="update_password_password_confirmation">{{ __('Confirmar contraseña') }}</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control"
                autocomplete="new-password"
            />
        </div>

        <div class="col-12 d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line me-1"></i>{{ __('Guardar') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success ms-3">
                    <i class="ri-check-line me-1"></i>{{ __('Guardado.') }}
                </span>
            @endif
        </div>
    </form>
</section>
