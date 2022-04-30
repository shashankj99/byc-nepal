@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Customer Accounts</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('customer.account.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Account") }}
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
    @if(auth()->user()->hasRole("Admin"))
        <form action="{{ route('customer.account') }}" method="GET">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4 col-sm-12 col-xs-12 mb-2">
                    <select class="form-control user-select" name="user_id" id="user-id">
                        @foreach($users as $user)
                            <option value="{{ $user["id"] }}"
                                    @if($user_id == $user["id"]) selected @endif>{{ $user["full_name"] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <button type="submit" class="btn btn-dark btn-block">View Customer Account</button>
                </div>
            </div>
        </form>
    @endif
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="account-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                @if(auth()->user()->hasRole("Admin"))
                                    <th>Customer Name</th>
                                @endif
                                <th>Name of Bank</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>BSB</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customer_accounts as $customer_account)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    @if(auth()->user()->hasRole("Admin"))
                                        <td>{{ $customer_account->user->full_name }}</td>
                                    @endif
                                    <td>{{ $customer_account->bank_name . ", ". $customer_account->branch }}</td>
                                    <td>{{ $customer_account->account_name }}</td>
                                    <td>{{ $customer_account->account_number }}</td>
                                    <td>{{ $customer_account->bsb }}</td>
                                    <td>
                                        @if($customer_account->is_default == "0")
                                            <a class="make-default badge badge-success" id="make-location-default"
                                               data-id="{{ $customer_account->id }}">
                                                Make default
                                            </a>
                                        @else
                                            <small class="badge badge-dark">Default</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ route('customer.account.edit', $customer_account->id) }}"
                                               class="btn btn-link text-dark">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-link text-dark delete-account"
                                               data-id="{{ $customer_account->id }}">
                                                <i class="fas fa-trash"></i>
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
@endsection

@section("page-scripts")
    <script src="{{ asset("js/jquery.datatables.js") }}"></script>
    <script src="{{ asset("js/datatables.bootstrap.js") }}"></script>
    <script>
        $(document).ready(function () {
            $("#account-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            // make location default
            $('#account-table').on('click', '.make-default', function () {
                if (confirm("Do you want to make this account your default?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route('customer.account.default', ':id') }}";
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

            $("#account-table").on("click", '.delete-account', function () {
                if (confirm("Are you sure you want to delete this account?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("customer.account.delete", ":id") }}";
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
