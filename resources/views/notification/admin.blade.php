@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Notifications</h1>
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
                <div class="card-header">
                    <h3 class="card-title">
                        Filter Notification Data
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route("notification") }}" method="GET">
                        <div class="form-group row">
                            <div class="col-md-4 col-sm-12 mb-3">
                                <label for="user_id">Filter By Customers</label>
                                <select class="form-control user-select" name="user_id" id="user_id">
                                    <option value="">Show all customers</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user["id"] }}"
                                                @if($user_id == $user["id"]) selected @endif>{{ $user["full_name"] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-3">
                                <label for="created_at">Filter By Order Date</label>
                                <input type="date" name="created_at" id="created_at" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <button type="submit" class="btn btn-dark btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="notification-table">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Customer Name</th>
                                <th>Pickup Id</th>
                                <th>Street Address</th>
                                <th>No Of Bins</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $notification->user->full_name }}</td>
                                    @isset($notification->pickup)
                                        <td>
                                            <a href="{{ route("pickup.edit", $notification->pickup->id) }}" class="btn btn-link">
                                                Pickup #{{ $notification->pickup->id }}
                                            </a>
                                        </td>
                                        @isset($notification->pickup->userAddress)
                                            <td>{{ $notification->pickup->userAddress->address }}</td>
                                        @endisset
                                        <td>{{ $notification->pickup->no_of_bins }}</td>
                                    @else
                                        <td> Not Found </td>
                                        <td> Not Found </td>
                                        <td> Not Found </td>
                                    @endif
                                    <td>{{ $notification->description }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a href="#" class="dropdown-item delete-notification" data-id="{{ $notification->id }}">
                                                Delete Notification
                                            </a>
                                        </div>
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
    <div class="modal fade" id="modal-assign-pickup-date" data-bs-backdrop="static" data-bs-keyboard="false"
         role="dialog">
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
            $("#notification-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            $("#notification-table").on("click", '.delete-notification', function () {
                if (confirm("Are you sure you want to delete this notification?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("notification.delete", ":id") }}";
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
