@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Order A Bin</h1>
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
    @error("bin_type")
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => $message])
            </div>
        </div>
    @enderror
    @error("subscription_id")
    <div class="row">
        <div class="col">
            @include("alerts.error", ["message" => $message])
        </div>
    </div>
    @enderror
    <div class="row mb-3">
        <div class="col">
            <h6>
                Which bin would you like?
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.order.bin") }}" method="POST">
        @csrf
        <input type="hidden" name="subscription_id" value="{{ $latest_subscription_id }}">
        @if($user_charity) <input type="hidden" name="charity_id" value="{{ $user_charity->id }}"> @endif
        <div class="row mb-3">
            <div class="col">
                <div class="card card-widget widget-user custom-box" id="drum-bin-card">
                    <div class="widget-user-header">
                        <h6>
                            <input type="radio" name="bin_type" id="drum-bin" class="form-check-input" value="drum-bin">
                            <label for="drum-bin" class="form-check-label mt-1">
                                200 litre drum
                            </label>
                        </h6>
                        <span>
                            The drums are delivered FREE.
                        </span>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ asset('images/img/Drum.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="card card-widget widget-user custom-box" id="wheelie-bin-card">
                    <div class="widget-user-header">
                        <h6>
                            <input type="radio" name="bin_type" id="wheelie-bin" class="form-check-input"
                                   value="wheelie-bin">
                            <label for="drum-bin" class="form-check-label mt-1">
                                240 litre wheelie-bin
                            </label>
                        </h6>
                        <span>
                            Wheelie-bins have a once-off Rs 400 which can be paid upfront or deducted in Rs 100 installments.
                        </span>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ asset('images/img/Wheelie-bin.png') }}" alt="">
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
