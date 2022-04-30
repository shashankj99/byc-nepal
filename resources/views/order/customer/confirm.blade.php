@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Order Details</h1>
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
            <h6>
                Confirm Your Order Details
            </h6>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="info-box bg-white text-dark">
                <span class="info-box-icon">
                    @if($pre_order->bin_type == "drum-bin")
                        <img src="{{ asset("images/img/Drum.png") }}" alt="Drum">
                    @else
                        <img src="{{ asset("images/img/Wheelie-bin.png") }}" alt="Drum">
                    @endif
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Subscription ({{ $pre_order->subscription->name }})</span>
                    <span class="info-box-number">
                        @if($pre_order->bin_type == "drum-bin")
                            200 litre drum bin
                        @else
                            240 litre wheelie-bin
                        @endif
                    </span>
                    @if($pre_order->subscription->name == "Personal")
                        <span>
                          Refunds will be deposited in the nominated bank account
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-evenly mt-3 mb-3">
        <a class="btn btn-lg btn-light" href="{{ route('customer.subscription.edit', $pre_order->id) }}">
            {{ __('Edit Order Details') }}
        </a>
    </div>
    @if(isset($pre_order->user->customerAccounts[0]) && $pre_order->subscription->name == "Personal")
    <div class="row mb-3">
        <div class="col">
            <div class="info-box bg-white">
                <div class="info-box-content">
                    <span class="info-box-test">Deposit Refunds To</span><hr>
                    <span class="progress-description">
                        <span>Account Number</span><br>
                        <span>{{ $pre_order->user->customerAccounts[0]->account_number }}</span><hr>
                        <span>{{ $pre_order->user->customerAccounts[0]->bank_name }}</span><br>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-evenly mt-3 mb-3">
        <a class="btn btn-lg btn-light"
           href="{{ route('customer.account.edit', $pre_order->user->customerAccounts[0]->id) }}">
            {{ __('Edit Account Details') }}
        </a>
    </div>
    @elseif($pre_order->subscription->name == "Charity")
        <div class="row mb-3">
            <div class="col">
                <span class="text-dark text-bold">
                    Since You've selected Charity as your subscription plan.
                    Your payouts will be deposited to your selected charity.
                </span>
            </div>
        </div>
    @else
        <div class="row mb-3">
            <div class="col">
                <span class="text-danger text-bold">
                    ** You've not added any accounts for receiving payout amounts.
                    Please add the details else you won't be able to receive them.
                </span>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-evenly mt-3 mb-3">
        <a class="btn btn-lg btn-dark btn-block"
           href="{{ route('customer.checkout') }}">
            {{ __('Place Order') }}
        </a>
    </div>
@endsection
