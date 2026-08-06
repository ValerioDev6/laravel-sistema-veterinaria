<x-guest-layout>
    <div>
        <h5 class="text-primary">Welcome Back !</h5>
        <p class="text-muted">Sign in to continue to Velzon.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-4">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="mb-3">
                <div class="float-end">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
                    @endif
                </div>
                <label class="form-label" for="password-input">Password</label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input" name="password" required autocomplete="current-password">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="auth-remember-check" name="remember">
                <label class="form-check-label" for="auth-remember-check">Remember me</label>
            </div>

            <div class="mt-4">
                <button class="btn btn-success w-100" type="submit">Sign In</button>
            </div>
        </form>
    </div>

    <div class="mt-5 text-center">
        <p class="mb-0">Don't have an account ? <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline"> Signup</a> </p>
    </div>
</x-guest-layout>
