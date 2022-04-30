@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('customer.account') }}" class="btn btn-dark">
                {{ __("View Accounts") }}
            </a>
        </div>
    </div>
@endsection

@section("content")
    @if(session()->has("error"))
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => session()->get("error")])
            </div>
        </div>
    @endif
    <div class="row mb-3">
        <div class="col">
            <h6 class="mb-3">
                Which bank account would you like your payout to be deposited?
            </h6>
            <span class="text-muted">
                This account will only be used to deposit your payout amounts into
            </span>
        </div>
    </div>
    <form action="{{ route('customer.account') }}" method="POST">
        @csrf
        @include("account._form", ["buttonText" => "Submit", "users" => $users])
    </form>
@endsection
