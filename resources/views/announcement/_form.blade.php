@isset($announcement)
    @if($announcement->image)
        <input type="hidden" name="old_image" value="{{ $announcement->image }}">
    @endif
@endisset
<div class="row mb-3">
    <div class="col-md-6 col-sm-12 mb-3">
        <div class="form-group">
            <label for="heading" class="form-label">Heading</label>
            <input type="text" class="form-control @error('heading') is-invalid @enderror"
                   placeholder="Announcement Heading" name="heading"
                   value="@isset($announcement){{ $announcement->heading }}@else{{ old('heading') }}@endisset" required>
            @error('heading')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label for="sub_heading" class="form-label">Sub Heading</label>
            <input type="text" class="form-control @error('sub_heading') is-invalid @enderror"
                   placeholder="Announcement Sub Heading" name="sub_heading"
                   value="@isset($announcement){{ $announcement->sub_heading }}@else{{ old('sub_heading') }}@endisset">
            @error('sub_heading')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 col-sm-12 mb-3">
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description"
                      class="form-control @error('description') is-invalid @enderror" required>@isset($announcement){{ $announcement->description }}@else{{ old('description') }}@endisset</textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-3 col-sm-12 mb-3">
        <div class="form-group">
            <label for="publish_from" class="form-label">Publish From</label>
            <input type="date" class="form-control @error('publish_from') is-invalid @enderror" name="publish_from"
                   value="@isset($announcement){{ $announcement->publish_from }}@else{{ old('publish_from') }}@endisset"
                   required>
            @error('publish_from')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="form-group">
            <label for="publish_to" class="form-label">Publish To</label>
            <input type="date" class="form-control @error('publish_to') is-invalid @enderror" name="publish_to"
                   value="@isset($announcement){{ $announcement->publish_to }}@else{{ old('publish_to') }}@endisset"
                   required>
            @error('publish_to')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3 col-sm-12 mb-3">
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" name="status" id="status" required>
                <option value="active"
                        @if((isset($announcement) && ($announcement->status == "active")) || (old("status") == "active")) selected @endif>
                    Active
                </option>
                <option value="inactive"
                        @if((isset($announcement) && ($announcement->status == "inactive")) || (old("status") == "inactive")) selected @endif>
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

    @isset($announcement)
        @if($announcement->image)
            <div class="col-md-3 col-sm-12 mb-2">
                <img src="{{ asset("/images/announcements/{$announcement->image}") }}" alt="Announcement Image" class="img-fluid" style="max-height: 240px">
            </div>
        @endif
    @endisset

    <div class="col-md-3 col-sm-12">
        <div class="form-group">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image">
            @error('image')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <button type="submit" class="btn btn-block btn-dark">
            {{ $buttonText }}
        </button>
    </div>
</div>
