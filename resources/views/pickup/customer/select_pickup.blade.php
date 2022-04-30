@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Pickup Order</h1>
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
    @error("pickup_id")
    <div class="row">
        <div class="col">
            @include("alerts.error", ["message" => $message])
        </div>
    </div>
    @enderror
    @error("no_of_bins")
    <div class="row">
        <div class="col">
            @include("alerts.error", ["message" => $message])
        </div>
    </div>
    @enderror
    <div class="row mb-3">
        <div class="col">
            <h6>
                Select your pickup order whose date you want to change.
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.pickup.check") }}" method="POST">
        @csrf
        <div class="form-group row">
            <div class="col">
                @foreach($pickups as $pickup)
                    <div class="card mb-3" id="{{ $pickup->id }}">
                        <div class="card-body" role="button">
                            <dl>
                                <dt class="ml-2">
                                    <input class="form-check-input" id="{{ "pickup-{$pickup->id}" }}"
                                           type="radio" name="pickup_id"
                                           value="{{ $pickup->id }}" required>
                                    <label class="form-check-label mt-1">{{ $pickup->userAddress->address }}</label>
                                </dt>
                                <dd>
                                    <div class="row mt-3">
                                        <div class="col-10 mt-2">
                                            Number of bins
                                        </div>
                                        <div class="col-2">
                                            <input type="text" name="no_of_bins"
                                                   class="form-control" required readonly
                                                   value="{{ $pickup->no_of_bins }}">
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                @endforeach
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
