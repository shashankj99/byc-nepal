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
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="notification-table">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Street Address</th>
                                <th>No Of Bins</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    @isset($notification->pickup)
                                        @isset($notification->pickup->userAddress)
                                            <td>{{ $notification->pickup->userAddress->address }}</td>
                                        @endisset
                                        <td>{{ $notification->pickup->no_of_bins }}</td>
                                    @else
                                        <td> Not Found </td>
                                        <td> Not Found </td>
                                    @endisset
                                    <td>{{ $notification->description }}</td>
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
            $("#notification-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>
@endsection
