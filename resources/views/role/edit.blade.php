<div class="modal-header">
    <h4 class="modal-title">Edit Role</h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="{{ route('role.update', $role->id) }}" method="POST">
    @csrf
    {{ method_field('PUT') }}
    <div class="modal-body">
        <input type="text" class="form-control" placeholder="Enter the new role name" name="name"
               value="{{ $role->name }}" required>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-dark">Update</button>
    </div>
</form>
