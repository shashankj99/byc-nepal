@if(auth()->user()->hasRole("Admin"))
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="user_id" class="form-label">Select a customer</label>
                <select name="user_id" id="user_id" class="form-control @error('account_name') is-invalid @enderror">
                    @foreach($users as $user)
                        <option value="{{ $user["id"] }}"
                                @if(isset($customer_account) && ($user["id"] == $customer_account->user_id)) selected @endif>
                            {{ $user["full_name"] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="account_name" class="form-label">Account Name</label>
            <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                   placeholder="Name(s) Account is held under" name="account_name"
                   value="@isset($customer_account){{ $customer_account->account_name }}@else{{ old('account_name') }}@endisset"
                   required>
            @error('account_name')
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
            <label for="account_number" class="form-label">Account Number</label>
            <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                   placeholder="0000000000" name="account_number"
                   value="@isset($customer_account){{ $customer_account->account_number }}@else{{ old('account_number') }}@endisset"
                   required>
            @error('account_number')
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
            <label for="bsb" class="form-label">BSB</label>
            <input type="text" class="form-control @error('bsb') is-invalid @enderror"
                   placeholder="000-000" name="bsb"
                   value="@isset($customer_account){{ $customer_account->bsb }}@else{{ old('bsb') }}@endisset"
                   required>
            @error('bsb')
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
            <label for="bank_name" class="form-label">Bank Name</label>
            <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                   placeholder="Name Of Bank" name="bank_name"
                   value="@isset($customer_account){{ $customer_account->bank_name }}@else{{ old('bank_name') }}@endisset"
                   required>
            @error('bank_name')
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
            <label for="branch" class="form-label">Branch</label>
            <input type="text" class="form-control @error('branch') is-invalid @enderror"
                   placeholder="Branch" name="branch"
                   value="@isset($customer_account){{ $customer_account->branch }}@else{{ old('branch') }}@endisset"
                   required>
            @error('branch')
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
