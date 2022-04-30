@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 mb-2">
            <h1 class="m-0">Notifications</h1>
        </div>
        <div class="col-md-2 col-sm-12 mb-2">
            <a href="{{ route("admin.notification.create") }}" class="btn btn-dark btn-block">
                Create Notification
            </a>
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
    @if(session()->has("success"))
        <div class="row">
            <div class="col">
                @include("alerts.success", ["message" => session()->get("success")])
            </div>
        </div>
    @endif
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="admin-notification-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Customer Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($admin_notifications as $notification)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $notification->user->full_name }}</td>
                                    <td>{{ $notification->description }}</td>
                                    <td>
                                        @if($notification->is_seen == "1")
                                            <span class="badge bg-success">Seen</span>
                                        @else
                                            <span class="badge bg-warning">Not Seen</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->created_at }}</td>
                                    <td>
                                        <a class="btn btn-link text-dark delete-notification"
                                           data-id="{{ $notification->id }}">
                                            <span class="fas fa-trash"></span> Delete
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
@endsection

@section("page-scripts")
    <script src="{{ asset("js/jquery.datatables.js") }}"></script>
    <script src="{{ asset("js/datatables.bootstrap.js") }}"></script>
    <script>
        $(document).ready(function () {
            $("#admin-notification-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });
        });

        $("#admin-notification-table").on("click", '.delete-notification', function () {
            if (confirm("Are you sure you want to delete this notification?")) {
                let id = this.dataset.id, defaultUrl = "{{ route("admin.notification.delete", ":id") }}";
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
    </script>
@endsection
