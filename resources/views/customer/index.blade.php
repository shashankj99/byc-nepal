@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    <link rel="stylesheet" href="{{ asset("css/datatables.button.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Customers</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12">
            <a href="{{ route('customer.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Customer") }}
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
                        <table class="table table-striped table-sm" id="customer-table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Customer ID</th>
                                <th>Myob UID</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Suburban</th>
                                <th>Postal Code</th>
                                <th>Onboard</th>
                                <th>Off board</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $customer["id"] }}</td>
                                    <td>{{ $customer["myob_uid"] }}</td>
                                    <td>{{ $customer["full_name"] }}</td>
                                    @isset($customer["user_addresses"][0])
                                        <td>{{ $customer["user_addresses"][0]["address"] }}</td>
                                        <td>{{ $customer["user_addresses"][0]["suburban"] }}</td>
                                        <td>{{ $customer["user_addresses"][0]["postal_code"] }}</td>
                                    @else
                                        <td>
                                            <a href="{{ route("location.create") }}" class="btn btn-dark btn-sm">
                                                <i class="fas fa-plus"> Add Address</i>
                                            </a>
                                        </td>
                                        <td>Not Specified</td>
                                        <td>Not Specified</td>
                                    @endisset
                                    <td>{{ $customer["created_at_formatted"] }}</td>
                                    <td>{{ $customer["off_board_at"] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item" href="{{ route("customer.show", $customer['id']) }}">View
                                                Customer Card</a>
                                            <a class="dropdown-item off-board" href="#" id="off-board"
                                               data-id="{{ $customer['id'] }}">Off-Board Customer</a>
                                            <a class="dropdown-item delete-customer" href="#" id="delete-customer"
                                               data-id="{{ $customer['id'] }}">Delete Customer Data</a>
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
            $('#customer-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excel",
                        title: "Customers",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                        text: '<i class="fas fa-fw fa-file-excel"></i> Download as Excel'
                    }
                ],
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            // make location default
            $('#customer-table').on('click', '.off-board', function () {
                if (confirm("Do you want to Off board this customer?")) {
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

            $("#customer-table").on("click", '.delete-customer', function () {
                if (confirm("Are you sure you want to delete this customer?")) {
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
