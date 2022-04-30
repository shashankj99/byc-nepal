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
    <form action="{{ route('customer.account.update', $customer_account->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        @include("account._form", ["customer_account" => $customer_account, "buttonText" => "Update", "users" => $users])
    </form>
@endsection

