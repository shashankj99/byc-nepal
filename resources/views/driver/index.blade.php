@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    <link rel="stylesheet" href="{{ asset("css/datatables.button.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Drivers</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('driver.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Driver") }}
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
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="driver-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Depot</th>
                                <th>Licence</th>
                                <th>Device No</th>
                                <th>Route</th>
                                <th>On Board</th>
                                <th>Off Board</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->driver->depot }}</td>
                                    <td>{{ $user->driver->license }}</td>
                                    <td>{{ $user->driver->device_number }}</td>
                                    <td>{{ $user->driver->route }}</td>
                                    <td>{{ $user->created_at_formatted }}</td>
                                    <td>{{ $user->off_board_at }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item" href="{{ route("driver.edit", $user->id) }}">Edit Driver
                                                Details</a>
                                            <a class="dropdown-item off-board" href="#" id="off-board"
                                               data-id="{{ $user->id }}">Off-Board Driver</a>
                                            <a class="dropdown-item delete-customer" href="#" id="delete-driver"
                                               data-id="{{ $user->id }}">Delete Driver</a>
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
@endsection

@section("page-scripts")
    <script src="{{ asset("js/jquery.datatables.js") }}"></script>
    <script src="{{ asset("js/datatables.bootstrap.js") }}"></script>
    <script src="{{ asset("js/datatables.button.js") }}"></script>
    <script src="{{ asset("js/datatables.jszip.js") }}"></script>
    <script src="{{ asset("js/datatables.button.html5.js") }}"></script>
    <script>
        $(document).ready(function () {
            $('#driver-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excel",
                        title: "Drivers",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        },
                        text: '<i class="fas fa-fw fa-file-excel"></i> Download as Excel'
                    }
                ]
            });

            // make location default
            $('#driver-table').on('click', '.off-board', function () {
                if (confirm("Do you want to Off board this driver?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route('customer.off-board', ':id') }}";
                    defaultUrl = defaultUrl.replace(":id", id);
                    $.ajax({url: defaultUrl, method: "GET"})
                        .done(function (res) {
                            alert(res.message);
                        })
                        .fail(function (xhr) {
                            alert(xhr.statusText);
                        })
                        .always(function () {
                            window.location.reload();
                        })
                } else return false;
            });

            $("#driver-table").on("click", '.delete-customer', function () {
                if (confirm("Are you sure you want to delete this driver?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("customer.delete", ":id") }}";
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
