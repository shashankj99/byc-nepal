<div class="modal-header">
    <h4 class="modal-title">Assign Bin</h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="{{ route('bin.assign') }}" method="POST">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="order_id" value="{{ $order_id }}">
        <div class="form-group row">
            <div class="col">
                <label for="bin_number" class="form-label">Select a bin number</label>
                <select name="bin_id" id="bin-id" class="form-control assign-bin-select">
                    @foreach($bins as $bin)
                        <option value="{{ $bin->id }}">
                            {{ $bin->bin_number }} ({{ $bin->bin_type }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-dark">Allocate</button>
    </div>
</form>
