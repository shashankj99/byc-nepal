@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Roles</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <button type="button" class="btn btn-block btn-dark" data-bs-toggle="modal"
                    data-bs-target="#modal-add-role">
                {{ __("Add New Role") }}
            </button>
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
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="role-table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $role["name"] }}</td>
                                    <td>
                                        <a class="btn btn-link text-dark edit-role" data-id="{{ $role['id'] }}">
                                            <span class="fas fa-edit"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-link text-dark delete-role" data-id="{{ $role['id'] }}">
                                            <span class="fas fa-trash"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add-role" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Role</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('role') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="text" class="form-control" placeholder="Enter the new role name" name="name"
                               required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-edit-role" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
        </div>
    </div>
@endsection

@section("page-scripts")
    <script src="{{ asset("js/jquery.datatables.js") }}"></script>
    <script src="{{ asset("js/datatables.bootstrap.js") }}"></script>
    <script>
        $(document).ready(function () {
            $('#role-table').DataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            // delete location
            $("#role-table").on("click", '.edit-role', function () {
                let id = this.dataset.id, defaultUrl = "{{ route("role.edit", ":id") }}";
                defaultUrl = defaultUrl.replace(":id", id);
                $.ajax({url: defaultUrl, method: "GET"})
                    .done(function (res) {
                        $('#modal-edit-role .modal-content').html(res);
                        $('#modal-edit-role').modal('show');
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });
            });

            // delete location
            $("#role-table").on("click", '.delete-role', function () {
                if (confirm("Are you sure you want to delete this role?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("role.delete", ":id") }}";
                    defaultUrl = defaultUrl.replace(":id", id);
                    $.ajax({
                        url: defaultUrl,
                        method: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        }
                    })
                        .done(function (res) {
                            alert(res.message);
                        })
                        .fail(function (xhr) {
                            alert(xhr.statusText);
                        })
                        .always(function () {
                            window.location.reload();
                        });
                } else return false;
            });
        });
    </script>
@endsection
