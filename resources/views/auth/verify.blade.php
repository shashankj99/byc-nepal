@extends('layouts.app')

@section('login-content')
    <div class="login-box">
        @if(session()->has("error"))
            @include("alerts.error", ["message" => session()->get("error")])
        @endif
        @if(session()->has("success"))
            @include("alerts.success", ["message" => session()->get("success")])
        @endif
        @if(auth()->user()->status == "inactive")
            <div class="mb-5">
                <h6 class="text-bold mb-3">Check your email and click on the verification link to continue</h6>
                <p class="text-muted"> A verification mail has been sent to the following email
                    address {{ auth()->user()->email }}</p>
            </div>
            <div class="my-5 text-center">
                <a class="btn btn-link text-dark m-0 p-0" href="{{ route('resend') }}">
                    {{ __('Resend verification email') }}
                </a>
            </div>
        @else
            @include("alerts.success", ["message" => "Successfully verified email. Click next to continue"])
        @endif
        <div class="mt-5 d-flex justify-content-evenly">
            <a class="btn btn-dark btn-block @if(auth()->user()->status == "inactive") disabled @endif"
               href="{{ route('login') }}">
                {{ __('Next') }}
            </a>
        </div>
    </div>
@endsection
