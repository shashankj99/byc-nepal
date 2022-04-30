@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Notification</h1>
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
    <div class="row mb-3">
        <div class="col">
            <h6>
                Notify Backyard Cash of any changes
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.notification") }}" method="POST">
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
                                                   class="form-control" required
                                                   value="{{ $pickup->no_of_bins }}" readonly>
                                        </div>
                                    </div>
                                </dd>
                                <dd>
                                    <div class="row mt-3">
                                        <div class="col-6 mt-2">
                                            Your Pickup date is
                                        </div>
                                        <div class="col-6 mt-2 text-right">
                                            {{ $pickup->pickup_date_formatted }}
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row mb-3">
            <div class="col">
                <label for="description" class="form-label">Enter Your Description</label>
                <textarea name="description" id="description" cols="30" rows="10"
                          class="form-control @error("description") is-invalid @enderror" required></textarea>
                @error("description")
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
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
