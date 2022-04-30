@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Payment</h1>
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
            <h6 class="mb-3">
                Provide your Credit/Debit card details
            </h6>
        </div>
    </div>
    <form action="{{ route("customer.checkout") }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label for="card_name" class="form-label">Name On Card</label>
                    <input type="text" class="form-control @error('card_name') is-invalid @enderror"
                           placeholder="Name on card" name="card_name"
                           value="{{ old('card_name') }}"
                           required>
                    @error('card_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" class="form-control @error('card_number') is-invalid @enderror"
                           placeholder="123XXXXXXX" name="card_number"
                           value="{{ old('card_number') }}"
                           required>
                    @error('card_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex justify-content-evenly">
                    <div class="form-group mr-2">
                        <label for="cvc" class="form-label">CVC</label>
                        <input type="text" class="form-control @error('cvc') is-invalid @enderror"
                               placeholder="123" name="cvc"
                               value="{{ old('cvc') }}"
                               required>
                        @error('cvc')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mr-2">
                        <label for="exp_month" class="form-label">Exp Month</label>
                        <input type="text" class="form-control @error('exp_month') is-invalid @enderror"
                               placeholder="2" name="exp_month"
                               value="{{ old('exp_month') }}"
                               required>
                        @error('exp_month')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exp_year" class="form-label">Exp Year</label>
                        <input type="text" class="form-control @error('exp_year') is-invalid @enderror"
                               placeholder="1997" name="exp_year"
                               value="{{ old('exp_year') }}"
                               required>
                        @error('exp_year')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <button type="submit" class="btn btn-block btn-dark">
                    Next
                </button>
            </div>
        </div>
    </form>
@endsection
