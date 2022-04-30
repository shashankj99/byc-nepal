@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('admin.notification') }}" class="btn btn-dark">
                {{ __("View Notifications") }}
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
    <form action="{{ route('admin.notification') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label for="user_id" class="form-label">Select customer/s</label>
                    <select name="user_id" id="user_id"
                            class="form-control @error('user_id') is-invalid @enderror">
                        <option value="all">All Customers</option>
                        @foreach($customers as $user)
                            <option value="{{ $user->id }}" @if(old("user_id") == $user->id) selected @endif>
                                {{ $user->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error("user_id")
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
                    <label for="description" class="form-label">Notification Message</label>
                    <textarea name="description" cols="30" rows="10"
                              class="form-control @error("description") is-invalid @enderror"
                              id="description">{{ old("description") }}</textarea>
                    @error("description")
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <button type="submit" class="btn btn-block btn-dark">
                    Send Notification
                </button>
            </div>
        </div>
    </form>
@endsection
