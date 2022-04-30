@extends('layouts.app')

@section('login-content')
    <div class="login-box">
        @if(session()->has("error"))
            @include("alerts.error", ["message" => session()->get("error")])
        @endif
        <div class="my-3">
            <h5 class="text-dark">Create your account</h5>
            @if (Route::has('login'))
                <a class="btn btn-link text-dark m-0 p-0" href="{{ route('login') }}">
                    {{ __('Already have an account? Sign In') }}
                </a>
            @endif
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-6">
                    <label for="first_name"
                           class="col-form-label">{{ __('First Name') }}</label>

                    <input id="first_name" type="text"
                           class="form-control @error('first_name') is-invalid @enderror"
                           name="first_name" value="{{ old('first_name') }}" required placeholder="First Name">

                    @error('first_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-6">
                    <label for="last_name"
                           class="col-form-label">{{ __('Last Name') }}</label>

                    <input id="last_name" type="text"
                           class="form-control @error('last_name') is-invalid @enderror"
                           name="last_name" value="{{ old('last_name') }}" required placeholder="Last Name">

                    @error('last_name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="mobile_number"
                           class="col-form-label">{{ __('Mobile Number') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                +61
                            </div>
                        </div>
                        <input id="mobile_number" type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                               name="mobile_number" value="{{ old('mobile_number') }}" required>

                        @error('mobile_number')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="email"
                           class="col-form-label">{{ __('Email Address') }}</label>

                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="name@gmail.com">

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="password"
                           class="col-form-label">{{ __('Password') }}</label>

                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" value="{{ old('password') }}" required placeholder="********">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="password_confirmation"
                           class="col-form-label">{{ __('Confirm Password') }}</label>

                    <input id="password_confirmation" type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           name="password_confirmation" value="{{ old('password_confirmation') }}" required placeholder="********">

                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <p class="m-0 p-0 text-muted">
                        By signing up you agree to our Terms & Conditions and Privacy Policy
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-dark btn-block">
                        {{ __('Next') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
