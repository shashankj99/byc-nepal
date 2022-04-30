@extends("layouts.app")

@section("content-header")
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Change pickup Date</h1>
        </div>
    </div>
@endsection

@section("content")
    <form action="{{ route("customer.pickup.date.update", $pickup->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        <div class="form-group row">
            <div class="col-md-4 col-6 mb-3">
                <label for="old_date" class="form-label">Current Pickup Date</label>
                <input type="date" name="old_date" id="old-date"
                       class="form-control @error("old_date") is-invalid @enderror" value="{{ $old_date }}" required
                       readonly>
                @error('old_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-2 col-6 mb-3">
                <label for="old_time" class="form-label">Current Pickup Time</label>
                <input type="time" name="old_time" id="old-time"
                       class="form-control @error("old_time") is-invalid @enderror" value="{{ $old_time }}" required
                       readonly>
                @error('old_time')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4 col-6 mb-3">
                <label for="new_date" class="form-label">New Pickup Date</label>
                <input type="date" name="new_date" id="new-date"
                       class="form-control @error("new_date") is-invalid @enderror" required>
                @error('new_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-2 col-6 mb-3">
                <label for="new_time" class="form-label">New Pickup Time</label>
                <input type="time" name="new_time" id="new-time"
                       class="form-control @error("new_time") is-invalid @enderror" required>
                @error('new_time')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <button type="submit" class="btn btn-block btn-dark">
                    Next
                </button>
            </div>
        </div>
    </form>
@endsection
