@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Pickup Orders</h1>
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
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="order-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Pickup ID</th>
                                <th>Street Address</th>
                                <th>No Of Bins</th>
                                <th>Pickup Date</th>
                                <th>Pickup Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pickups as $pickup)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $pickup->id }}</td>
                                    <td>{{ $pickup->userAddress->address }}</td>
                                    <td>{{ $pickup->no_of_bins }}</td>
                                    <td>{{ $pickup->pickup_date }}</td>
                                    <td>
                                        @if($pickup->status == "pending")
                                            <span class="badge badge-pill badge-warning">Pending</span>
                                        @elseif($pickup->status == "rejected")
                                            <span class="badge badge-pill badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-pill badge-success">Accepted</span>
                                        @endif
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
            $("#order-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>
@endsection
