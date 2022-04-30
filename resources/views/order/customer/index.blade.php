@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Bin Orders</h1>
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
                    <div class="table-responsive table-striped table-sm">
                        <table class="table" id="order-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Subscription</th>
                                <th>Charity</th>
                                <th>Card Type</th>
                                <th>Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Bin Type</th>
                                <th>Payment Type</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->full_name }}</td>
                                    <td>{{ $order->subscription->name }}</td>
                                    <td>
                                        @if($order->charity)
                                            <span>{{ $order->charity }}</span>
                                        @else
                                            <span>Not Selected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->card_type)
                                            <span>{{ $order->card_type }}</span>
                                        @else
                                            <span>NULL</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->amount }}</td>
                                    <td>
                                        @if($order->order_status == "pending")
                                            <span class="badge badge-pill badge-warning">Pending</span>
                                        @elseif($order->order_status == "rejected")
                                            <span class="badge badge-pill badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-pill badge-success">Accepted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status == "complete")
                                            <span class="badge badge-pill badge-success">Complete</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">Incomplete</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->bin_type }}</td>
                                    <td>{{ $order->payment_type }}</td>
                                    <td>{{ $order->created_at }}</td>
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
