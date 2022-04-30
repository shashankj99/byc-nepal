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
                Would you like to change your bin type?
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.order.update", $pre_order->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        <div class="row mb-3">
            <div class="col">
                <div class="card card-widget widget-user custom-box" id="drum-bin-card">
                    <div class="widget-user-header">
                        <h6>
                            <input type="radio" name="bin_type" id="drum-bin" class="form-check-input" value="drum-bin"
                                   @if($pre_order->bin_type == "drum-bin") checked @endif>
                            <label for="drum-bin" class="form-check-label mt-1">
                                200 litre drum
                            </label>
                        </h6>
                        <span>
                            The drums are delivered free of charge.
                        </span>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ asset('images/img/dustbin2.jpg') }}" alt="">
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
                                   value="wheelie-bin" @if($pre_order->bin_type == "wheelie-bin") checked @endif>
                            <label for="drum-bin" class="form-check-label mt-1">
                                240 litre wheelie-bin
                            </label>
                        </h6>
                        <span>
                            Wheelie-bins have a once off Rs 400 which can be paid upfront or deducted in Rs 100 installments.
                        </span>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ asset('images/img/dustbin.jpeg') }}" alt="">
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
