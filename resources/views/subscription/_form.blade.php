<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <div class="form-group">
            <label for="name" class="form-label">Subscription Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   placeholder="Personal" name="name"
                   value="@isset($subscription){{ $subscription->name }}@else{{ old('name') }}@endisset" required>
            @error('name')
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
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description"
                      class="form-control @error('description') is-invalid @enderror" required>
                @isset($subscription){{ $subscription->description }}@else {{ old('description') }}@endisset
            </textarea>
            @error('description')
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
