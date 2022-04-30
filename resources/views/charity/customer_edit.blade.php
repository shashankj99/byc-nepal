@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Charities</h1>
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
                Choose a charity to donate your refund to.
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.charity.update") }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        <input type="hidden" name="pre_order_id" value="{{ $pre_order_id }}">
        <div class="form-group row mb-3">
            <div class="col">
                <div class="card-deck">
                    @foreach($charities as $charity)
                        <div id="{{ $charity->name }}" class="card mb-4">
                            <div class="card-body ml-2" role="button">
                                <h5 class="card-title">
                                    <input class="form-check-input" id="{{ "charity-{$charity->id}" }}"
                                           type="radio" name="charity_id"
                                           value="{{ $charity->id }}"
                                           @isset($user_charity) @if($user_charity->charity_id == $charity->id) checked @endif @endisset
                                           required>
                                    <label class="form-check-label mt-1">{{ $charity->name }}</label>
                                </h5>
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
