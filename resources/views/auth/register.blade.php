<x-guest-layout>
    <div>
        <h5 class="text-primary">Register Account</h5>
        <p class="text-muted">Get your Free Velzon account now.</p>
    </div>

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
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>

            <div class="mb-3">
                <label for="useremail" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter email address" value="{{ old('email') }}" required autocomplete="username">
            </div>

            <div class="mb-3">
                <label class="form-label" for="password-input">Password</label>
                <div class="position-relative auth-pass-inputgroup">
                    <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input" name="password" required autocomplete="new-password">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password-confirm-input">Confirm Password</label>
                <div class="position-relative auth-pass-inputgroup">
                    <input type="password" class="form-control pe-5 password-input" placeholder="Confirm password" id="password-confirm-input" name="password_confirmation" required autocomplete="new-password">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-confirm-addon"><i class="ri-eye-fill align-middle"></i></button>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-success w-100" type="submit">Sign Up</button>
            </div>
        </form>
    </div>

    <div class="mt-5 text-center">
        <p class="mb-0">Already have an account ? <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Signin</a> </p>
    </div>
</x-guest-layout>
