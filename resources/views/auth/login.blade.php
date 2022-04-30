@extends('layouts.app')

@section('login-content')
    <div class="login-box">
        @if(session()->has("error"))
            @include("alerts.error", ["message" => session()->get("error")])
        @endif
        <div class="my-5">
            <h5 class="text-dark">Sign In</h5>
        </div>
        <div class="mt-3">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email">{{ __("Email Address or Mobile Number") }}</label>
                <div class="input-group mb-3">
                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="Enter Email / Mobile Number">

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <label for="password">{{ __("Password") }}</label>
                <div class="input-group mb-3">
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror" name="password" required
                           placeholder="********">

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

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember"
                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>

                <div class="mb-5">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link text-dark p-0" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>

                <div class="row mt-5">
                    <div class="col-6">
                        <a class="btn btn-light btn-block" href="{{ route('register') }}">
                            {{ __('Sign Up') }}
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-dark btn-block">
                            {{ __('Sign In') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
