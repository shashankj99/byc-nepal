<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="first_name" class="form-label">
                First Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                   placeholder="Jane" id="first_name" name="first_name"
                   value="@isset($user){{ $user->first_name }}@else{{ old('first_name') }}@endisset"
                   required>
            @error('first_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="last_name" class="form-label">
                Last Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                   placeholder="Doe" id="last_name" name="last_name"
                   value="@isset($user){{ $user->last_name }}@else{{ old('last_name') }}@endisset"
                   required>
            @error('last_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="mobile_number" class="form-label">
                Mobile Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                   placeholder="1234567890" id="mobile_number" name="mobile_number"
                   value="@isset($user){{ $user->mobile_number }}@else{{ old('mobile_number') }}@endisset"
                   required>
            @error('mobile_number')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="email" class="form-label">
                Email Address
                <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="jane@gmail.com" id="email" name="email"
                   value="@isset($user){{ $user->email }}@else{{ old('email') }}@endisset"
                   required>
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="depot" class="form-label">
                Depot
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('depot') is-invalid @enderror"
                   placeholder="551 Waterloo Corner Rd, Burton SA 5110" id="depot" name="depot"
                   value="@isset($user){{ $user->driver->depot }}@else{{ old('depot') }}@endisset"
                   required>
            @error('depot')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="license" class="form-label">
                License
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('license') is-invalid @enderror"
                   placeholder="123456" id="license" name="license"
                   value="@isset($user){{ $user->driver->license }}@else{{ old('license') }}@endisset"
                   required>
            @error('license')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="device_number" class="form-label">
                Device Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('device_number') is-invalid @enderror"
                   placeholder="123456" id="device_number" name="device_number"
                   value="@isset($user){{ $user->driver->device_number }}@else{{ old('device_number') }}@endisset"
                   required>
            @error('device_number')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="route" class="form-label">Route</label>
            <input type="text" class="form-control @error('route') is-invalid @enderror"
                   placeholder="https://getcircuit.com/token" id="route" name="route"
                   value="@isset($user){{ $user->driver->route }}@else{{ old('route') }}@endisset">
            @error('route')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
@if(isset($user))
    <div class="row mb-2">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="off_board_at" class="form-label">Off Board At</label>
                <input type="date" class="form-control @error('off_board_at') is-invalid @enderror" id="off_board_at"
                       value="@isset($user){{ $user->off_board_at }}@else{{ old('off_board_at') }}@endisset"
                       name="off_board_at">
                @error('off_board_at')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
@endif
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="status" class="form-label">
            Status
            <span class="text-danger">*</span>
        </label>
        <select class="form-control" name="status" id="status" required>
            <option value="active"
                    @if((isset($user) && ($user->status == "active")) || (old("status") == "active")) selected @endif>
                Active
            </option>
            <option value="inactive"
                    @if((isset($user) && ($user->status == "inactive")) || (old("status") == "inactive")) selected @endif>
                Inactive
            </option>
        </select>
        @error('status')
        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <button type="submit" class="btn btn-block btn-dark">
            {{ $buttonText }}
        </button>
    </div>
</div>
