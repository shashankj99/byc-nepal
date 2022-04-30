@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset('css/jqueryui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('location') }}" class="btn btn-dark">
                {{ __("View location(s)") }}
            </a>
        </div>
    </div>
@endsection

@section("content")
    <div class="row mb-5">
        <div class="col">
            <h6 class="text-dark">Please provide your location</h6>
            <span class="text-muted">Where will your bin(s) be kept?</span>
        </div>
    </div>
    @if(session()->has("error"))
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => session()->get("error")])
            </div>
        </div>
    @endif
    <form action="{{ route("location") }}" method="POST">
        @csrf
        @if(auth()->user()->hasRole("Admin"))
            <div class="form-group row mb-3">
                <div class="col-md-4 col-sm-12">
                    <label for="user-id">Select A Customer</label>
                    <select name="user_id" id="user-id"
                            class="form-control user-id @error("user_id") is-invalid @enderror">
                        @foreach($users as $user)
                            <option value="{{ $user["id"] }}">{{ $user["full_name"] }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        @endif
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12 col-xs-12">
                <label for="address" class="form-label">Find your address</label>
                <input type="text" class="form-control autocomplete @error('address') is-invalid @enderror"
                       placeholder="Start Typing..." name="address"
                       id="address" autocomplete="off"/>
                @error('address')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="hidden" class="form-control" name="address_key" id="address-key"/>
            </div>
        </div>
        <div class="form-group row mb-3">
            <div class="col-md-4 col-sm-12">
                <label for="postal_code">Postcode</label>
                <select name="postal_code" id="postal-code"
                        class="form-control postal-code @error("postal_code") is-invalid @enderror" required>
                    @foreach($postal_codes as $postal_code)
                        <option value="{{ $postal_code }}">{{ $postal_code }}</option>
                    @endforeach
                </select>
                @error('postal_code')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <span class="text-dark">
                    Address Type
                </span>
            </div>
        </div>
        <div class="form-group mb-5">
            <div class="row">
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body" id="residential-card">
                            <div class="form-check">
                                <input class="form-check-input" id="residential" type="radio" name="type"
                                       value="Residential">
                                <label class="form-check-label">Residential</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body" id="business-card">
                            <div class="form-check">
                                <input class="form-check-input" id="business" type="radio" name="type"
                                       value="Business">
                                <label class="form-check-label">Business</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @error('type')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-12 col-sm-6">
                <button type="submit" class="btn btn-block btn-dark">
                    Next
                </button>
            </div>
        </div>
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset("js/custom/card-radio.js") }}"></script>
    <script src="{{ asset('js/jqueryui.js') }}"></script>
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(".postal-code").select2();
        $(".user-id").select2();

        // initialize empty arrays
        let data = [], dataKey = [];

        // fetch data from google place api and add it to the input field
        $('input[name="address"]').on('keyup', function () {
            let q = $(this).val();
            const addressUrl = "{{ route('google.address') }}";
            $.get(addressUrl, {q})
                .done(function (res) {
                    data = res.places;
                    dataKey = res.places_key;
                    $(".autocomplete").autocomplete({
                        source: data
                    });
                })
                .fail(function (xhr) {
                    alert(xhr.statusText);
                });
        });

        // add place key to the hidden input field
        $(".autocomplete").on("autocompleteclose", function () {
            let inputValue = $(this).val();
            $.each(data, function (index, value) {
                if (value.value === inputValue)
                    $("#address-key").val(dataKey[index]["place_id"]);
            });
        });
    </script>
@endsection
