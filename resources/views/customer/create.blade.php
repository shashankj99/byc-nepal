@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('customer') }}" class="btn btn-dark">
                {{ __("View Customers") }}
            </a>
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
    <form action="{{ route("customer") }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label for="first-name" class="form-label">First Name</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                           placeholder="Jane" value="{{ old('first_name') }}" name="first_name" required>
                    @error("first_name")
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
                    <label for="last-name" class="form-label">Last Name</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                           placeholder="Doe" value="{{ old('last_name') }}" name="last_name" required>
                    @error("last_name")
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
                    <label for="mobile-number" class="form-label">Mobile Number</label>
                    <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                           placeholder="9807060707" value="{{ old('mobile_number') }}" name="mobile_number" required>
                    @error("mobile_number")
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
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           placeholder="name@gmail.com" value="{{ old('email') }}" name="email" required>
                    @error("email")
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12">
                <button type="submit" class="btn btn-block btn-dark">Create</button>
            </div>
        </div>
    </form>
@endsection
