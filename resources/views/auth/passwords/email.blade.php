@extends('layouts.app')

@section('login-content')
    <div class="login-box">
        @if(session()->has("error"))
            <div class="row">
                <div class="col">
                    @include("alerts.error", ["message" => session()->get("error")])
                </div>
            </div>
        @endif
        @if(session()->has("success"))
            <div class="row">
                <div class="col">
                    @include("alerts.success", ["message" => session()->get("success")])
                </div>
            </div>
        @endif
        <div class="mt-3">
            <form method="POST" action="{{ route('send.reset.link') }}">
                @csrf
                <div class="mb-2">
                    <label for="email">{{ __("Email Address") }}</label>
                </div>
                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="name@gmail.com">
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
                <div class="row mb-3">
                    <div class="col">
                        <button type="submit" class="btn btn-dark btn-block">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
