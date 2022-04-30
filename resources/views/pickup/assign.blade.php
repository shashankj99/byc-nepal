<div class="modal-header">
    <h4 class="modal-title">Assign Pickup Date</h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="{{ route('pickup.assign', $pickup->id) }}" method="POST">
    @csrf
    {{ method_field("PUT") }}
    <div class="modal-body">
        <div class="form-group row">
            <div class="col">
                <input type="date" name="pickup_date" id="pick-up-date" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-dark">Allocate</button>
    </div>
</form>
