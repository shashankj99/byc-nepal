<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="user-id" class="form-label">Select A Customer</label>
        <select name="user_id" id="user-id"
                class="form-control user-id @error("user_id") is-invalid @enderror" required>
            @foreach($users as $user)
                <option value="{{ $user["id"] }}"
                        @if((isset($pickup) && ($pickup->user_id == $user["id"])) || (old("user_id") == $user["id"])) selected @endif>
                    {{ $user["full_name"] }}
                </option>
            @endforeach
        </select>
        @error('user_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="from-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="user-address-id" class="form-label">Address</label>
        <select name="user_address_id" id="user-address-id"
                class="form-control user-address-id @error("user_address_id") is-invalid @enderror">
        </select>
        @error('user_address_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="no-of-bins" class="form-label">No Of Bins</label>
        <input type="text" class="form-control @error("no_of_bins") is-invalid @enderror" name="no_of_bins"
               id="no-of-bins"
               value="@isset($pickup) {{ $pickup->no_of_bins }} @else {{ old("no_of_bins") }} @endisset"
               required>
        @error("no_of_bins")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4 col-sm-12">
        <label for="pick-up-date" class="form-label">Pickup Date</label>
        <input type="date" name="pickup_date" id="pick-up-date"
               class="form-control @error("pickup_date") is-invalid @enderror"
               value="@isset($pickup){{ $pickup->pickup_date }}@else{{ old("pickup_date") }}@endisset">
        @error("pickup_date")
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="status" class="form-label">Pickup Status</label>
        <select name="status" id="status" class="form-control @error("status") is-invalid @enderror"
                required>
            <option value="pending"
                    @if((isset($pickup) && $pickup->status == "pending") || (old("status") == "pending")) selected @endif>
                Pending
            </option>
            <option value="accepted"
                    @if((isset($pickup) && $pickup->status == "accepted") || (old("status") == "accepted")) selected @endif>
                Accepted
            </option>
            <option value="rejected"
                    @if((isset($pickup) && $pickup->status == "rejected") || (old("status") == "rejected")) selected @endif>
                Rejected
            </option>
        </select>
        @error("status")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <button type="submit" class="btn btn-dark btn-block">
            {{ $buttonText }}
        </button>
    </div>
</div>
