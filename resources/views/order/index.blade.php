@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    <link rel="stylesheet" href="{{ asset("css/datatables.button.css") }}">
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Bin Orders</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12">
            <a href="{{ route('order.create') }}" class="btn btn-block btn-dark">
                {{ __("Create Bin Order") }}
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
                        Filter Order Data
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route("orders") }}" method="GET">
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
                                <input type="date" name="created_at" id="created-at" class="form-control" />
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
                        <table class="table table-striped table-sm" id="order-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Subscription</th>
                                <th>Charity</th>
                                <th>Card Type</th>
                                <th>Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Bin Type</th>
                                <th>Payment Type</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->full_name }}</td>
                                    <td>{{ $order->userAddress->address }}</td>
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
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            @if($order->order_status != "accepted")
                                                <a class="dropdown-item assign-bin" href="#" data-id="{{ $order->id }}">Assign
                                                    Bin</a>
                                            @endif
                                            <a class="dropdown-item edit-order" href="{{ route("order.edit", $order->id) }}">Edit Order</a>
                                            <a class="dropdown-item delete-order" href="#" data-id="{{ $order->id }}">Delete Order</a>
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
    <div class="modal fade" id="modal-assign-bin" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
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
            $("#order-table").dataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excel",
                        title: "Bin Orders",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
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
            $("#order-table").on("click", '.assign-bin', function () {
                let id = this.dataset.id, defaultUrl = "{{ route("bin.assign.show", ":id") }}";
                defaultUrl = defaultUrl.replace(":id", id);
                $.ajax({url: defaultUrl, method: "GET"})
                    .done(function (res) {
                        $('#modal-assign-bin .modal-content').html(res);
                        $('#modal-assign-bin').modal('show');
                        $(".assign-bin-select").select2();
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });
            });

            $("#order-table").on("click", '.delete-order', function () {
                if (confirm("Are you sure you want to delete this order?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("order.delete", ":id") }}";
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
