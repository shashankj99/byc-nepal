@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Payments</h1>
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
                How would you like to pay for your 240 litre wheelie-bin?
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.payment.type") }}" method="POST">
        @csrf
        <input type="hidden" name="pre_order_id" value="{{ $pre_order_id }}">
        <div class="form-group row mb-3">
            <div class="col">
                <div class="card-deck">
                    <div id="full-payment" class="card mb-4">
                        <div class="card-body ml-2" role="button">
                            <h5 class="card-title">
                                <input class="form-check-input" id="full"
                                       type="radio" name="payment_type"
                                       value="full" required>
                                <label class="form-check-label mt-1">
                                    Pay the full amount now
                                </label>
                            </h5>
                        </div>
                    </div>
                    <div id="installment-payment" class="card mb-4">
                        <div class="card-body ml-2" role="button">
                            <h5 class="card-title">
                                <input class="form-check-input" id="installment"
                                       type="radio" name="payment_type"
                                       value="installment" required>
                                <label class="form-check-label mt-1">
                                    Deduct the amount from my payout
                                </label>
                            </h5>
                            <p class="card-text text-muted">
                                Selecting this option will deduct 4 * $10 from the first four payout amounts
                            </p>
                        </div>
                    </div>
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
