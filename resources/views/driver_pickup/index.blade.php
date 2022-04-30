@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Driver Pickups</h1>
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

    @if($is_admin)
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
                        <form action="{{ route("driver.pickup") }}" method="GET">
                            <div class="form-group row">
                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="driver-id">Filter By Drivers</label>
                                    <select class="form-control user-select" name="driver_id" id="driver-id">
                                        <option value="">Show all Drivers</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                    @if($driver_id == $driver->id) selected @endif>{{ $driver->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="pickup_date">Filter By Pickup Date</label>
                                    <input type="date" name="pickup_date" id="pickup_date" class="form-control"/>
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
    @endif

    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="driver-pickup-table">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Pickup Id</th>
                                <th>Customer Name</th>
                                @if($is_admin)
                                    <th>Driver Name</th>
                                @endif
                                <th>Street Address</th>
                                <th>Suburb</th>
                                <th>Postal Code</th>
                                <th>Bin No</th>
                                <th>Pickup Date</th>
                                <th>Pickup Status</th>
                                @if($is_admin)
                                    <th>Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($driver_pickups as $pickup)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $pickup->id }}</td>
                                    @isset($pickup->user)
                                        <td>{{ $pickup->user->full_name }}</td>
                                    @else
                                        <td>Not found</td>
                                    @endisset
                                    @if($is_admin && isset($pickup->driver))
                                        <td>{{ $pickup->driver->full_name }}</td>
                                    @endif
                                    @isset($pickup->userAddress)
                                        <td>{{ $pickup->userAddress->address }}</td>
                                        <td>{{ $pickup->userAddress->suburban }}</td>
                                        <td>{{ $pickup->userAddress->postal_code }}</td>
                                    @else
                                        <td>Not found</td>
                                        <td>Not found</td>
                                        <td>Not found</td>
                                    @endisset
                                    @isset($pickup->bin)
                                        <td>{{ $pickup->bin->bin_number }}</td>
                                    @else
                                        <td>Not found</td>
                                    @endisset
                                    <td>{{ $pickup->pickup_date }}</td>
                                    <td>
                                        @if($pickup->status == "unpicked")
                                            <span class="badge badge-pill badge-danger">Unpicked</span>
                                        @else
                                            <span class="badge badge-pill badge-success">Accepted</span>
                                        @endif
                                    </td>
                                    @if($is_admin)
                                        <td>
                                            <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a href="#" class="dropdown-item delete-driver-pickup" data-id="{{ $pickup->id }}">
                                                    Delete Pickup Order
                                                </a>
                                            </div>
                                        </td>
                                    @endif
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
            $("#driver-pickup-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            $("#driver-pickup-table").on("click", '.delete-driver-pickup', function () {
                if (confirm("Are you sure you want to delete this pickup?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("driver.pickup.delete", ":id") }}";
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
