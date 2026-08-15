<section>
    <p class="text-muted mb-4">
        {{ __('Actualiza la información de tu cuenta y tu correo electrónico.') }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="row g-3">
        @csrf
        @method('patch')

        <div class="col-12">
            <label class="form-label" for="name">{{ __('Nombre') }}</label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="email">{{ __('Correo electrónico') }}</label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-muted small mb-1">{{ __('Tu correo electrónico aún no está verificado.') }}</p>
                    <button form="send-verification" class="btn btn-link p-0 small">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-2">
                            {{ __('Se envió un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-12 d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line me-1"></i>{{ __('Guardar') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success ms-3">
                    <i class="ri-check-line me-1"></i>{{ __('Guardado.') }}
                </span>
            @endif
        </div>
    </form>
</section>
