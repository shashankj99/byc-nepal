<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="bin_number" class="form-label">Bin Number</label>
            <input type="text" class="form-control @error('bin_number') is-invalid @enderror"
                   placeholder="123XXXXXXX" name="bin_number"
                   value="@isset($bin){{ $bin->bin_number }}@else{{ old('bin_number') }}@endisset"
                   required>
            @error('bin_number')
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
            <label for="qr_code" class="form-label">QR Code</label>
            <input type="text" class="form-control @error('qr_code') is-invalid @enderror"
                   placeholder="123XXXXXXX" name="qr_code"
                   value="@isset($bin){{ $bin->qr_code }}@else{{ old('qr_code') }}@endisset"
                   required>
            @error('qr_code')
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
            <label for="bin_type" class="form-label">Select A Bin Type</label>
            <select class="form-control @error("bin_type") is-invalid @enderror" name="bin_type" id="bin-type" required>
                <option value="drum-bin"
                        @if((isset($bin) && ($bin->bin_type == "drum-bin")) || (old("bin_type") == "drum-bin")) selected @endif>
                    200 L Drum
                </option>
                <option value="wheelie-bin"
                        @if((isset($bin) && ($bin->bin_type == "wheelie-bin")) || (old("bin_type") == "wheelie-bin")) selected @endif>
                    240 L Wheelie Bin
                </option>
            </select>
            @error('bin_type')
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
            <label for="status" class="form-label">Bin Status</label>
            <select class="form-control @error("status") is-invalid @enderror" name="status" id="status" required>
                <option value="unallocated"
                        @if((isset($bin) && ($bin->status == "unallocated")) || (old("status") == "unallocated")) selected @endif>
                    Unallocated
                </option>
                <option value="allocated"
                        @if((isset($bin) && ($bin->status == "allocated")) || (old("status") == "allocated")) selected @endif>
                    Allocated
                </option>
            </select>
            @error('status')
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
            {{ $buttonText }}
        </button>
    </div>
</div>
