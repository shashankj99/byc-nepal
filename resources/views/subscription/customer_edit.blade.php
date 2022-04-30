@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Subscription</h1>
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
                Would you like to change your subscription?
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.subscription.update", $customer_subscription->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        <input type="hidden" name="pre_order_id" value="{{ $pre_order_id }}">
        <div class="form-group row mb-3">
            <div class="col">
                <div class="card-deck">
                    @foreach($subscriptions as $subscription)
                        <div id="{{ $subscription->name }}" class="card mb-4">
                            <div class="card-body ml-2" role="button">
                                <h5 class="card-title">
                                    <input class="form-check-input" id="{{ "subscription-{$subscription->id}" }}"
                                           type="radio" name="subscription_id"
                                           value="{{ $subscription->id }}"
                                           @if($customer_subscription->subscription_id == $subscription->id) checked @endif
                                           required>
                                    <label class="form-check-label mt-1">{{ $subscription->name }}</label>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ $subscription->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <button type="submit" class="btn btn-lg btn-dark btn-block">Next</button>
            </div>
        </div>
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset("js/custom/card-radio.js") }}"></script>
@endsection
