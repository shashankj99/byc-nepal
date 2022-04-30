<div class="login-box">
    <div class="my-5">
        <h5 class="text-dark">Change Password</h5>
    </div>
    <div class="mt-3">
        <form method="POST" action="{{ $route }}">
            @csrf
            @isset($token)
                <input type="hidden" name="token" value="{{ $token }}">
            @endisset
            <div class="mb-2">
                <label for="password">{{ __("New Password") }}</label>
            </div>
            <div class="input-group mb-3">
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" value="{{ old('password') }}" required placeholder="********">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>

                @error('password')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-2">
                <label for="password_confirmation">{{ __("Confirm Password") }}</label>
            </div>
            <div class="input-group mb-3">
                <input id="password_confirmation" type="password"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       name="password_confirmation" required
                       placeholder="********">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>

                @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col">
                    <button type="submit" class="btn btn-block btn-dark">
                        Next
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
