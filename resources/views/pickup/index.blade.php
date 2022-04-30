@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    <link rel="stylesheet" href="{{ asset("css/datatables.button.css") }}">
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-3">
            <h1 class="m-0">Pickup Orders</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12">
            <a href="{{ route('pickup.create') }}" class="btn btn-block btn-dark">
                {{ __("Create Pickup Order") }}
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
                <div class="card-header">
                    <h3 class="card-title">
                        Filter Pickup Data
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route("pickup") }}" method="GET">
                        <div class="form-group row">
                            <div class="col-md-4 col-sm-12 mb-3">
                                <label for="user_id">Filter By Customers</label>
                                <select class="form-control user-select" name="user_id" id="user-id">
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
                        <table class="table table-striped table-sm" id="pickup-table">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Pickup Id</th>
                                <th>Customer Name</th>
                                <th>Street Address</th>
                                <th>Suburb</th>
                                <th>Postal Code</th>
                                <th>No Of Bins</th>
                                <th>Pickup Date</th>
                                <th>Pickup Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pickups as $pickup)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $pickup->id }}</td>
                                    <td>{{ $pickup->user->full_name }}</td>
                                    <td>{{ $pickup->userAddress->address }}</td>
                                    <td>{{ $pickup->userAddress->suburban }}</td>
                                    <td>{{ $pickup->userAddress->postal_code }}</td>
                                    <td>{{ $pickup->no_of_bins }}</td>
                                    <td>{{ $pickup->pickup_date_formatted }}</td>
                                    <td>
                                        @if($pickup->status == "pending")
                                            <span class="badge badge-pill badge-warning">Pending</span>
                                        @elseif($pickup->status == "rejected")
                                            <span class="badge badge-pill badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-pill badge-success">Accepted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            @if($pickup->status != "accepted")
                                                <a class="dropdown-item assign-pickup-date" href="#"
                                                   data-id="{{ $pickup->id }}">Assign
                                                    Pickup Date</a>
                                                <a class="dropdown-item"
                                                   href="{{ route("pickup.accept", $pickup->id) }}">Accept
                                                    Order</a>
                                            @endif
                                            <a href="{{ route("pickup.edit", $pickup->id) }}" class="dropdown-item">
                                                Edit Pickup Order
                                            </a>
                                            <a href="#" class="dropdown-item delete-pickup" data-id="{{ $pickup->id }}">
                                                Delete Pickup Order
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
    <script src="{{ asset("js/datatables.button.js") }}"></script>
    <script src="{{ asset("js/datatables.jszip.js") }}"></script>
    <script src="{{ asset("js/datatables.button.html5.js") }}"></script>
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#pickup-table").dataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excel",
                        title: "Pickup Orders",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        },
                        text: '<i class="fas fa-fw fa-file-excel"></i> Download as Excel'
                    }
                ],
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });
            $(".user-select").select2();

            // ajax request to get assign bin form
            $("#pickup-table").on("click", '.assign-pickup-date', function () {
                let id = this.dataset.id, defaultUrl = "{{ route("pickup.assign", ":id") }}";
                defaultUrl = defaultUrl.replace(":id", id);
                $.ajax({url: defaultUrl, method: "GET"})
                    .done(function (res) {
                        $('#modal-assign-pickup-date .modal-content').html(res);
                        $('#modal-assign-pickup-date').modal('show');
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });
            });

            $("#pickup-table").on("click", '.delete-pickup', function () {
                if (confirm("Are you sure you want to delete this pickup order?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("pickup.delete", ":id") }}";
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
