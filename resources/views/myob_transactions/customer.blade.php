@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Refunds</h1>
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
                        <table class="table table-striped table-sm" id="refunds-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Deposit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($myob_transactions as $myob_transaction)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $myob_transaction->payment_date }}</td>
                                    <td>{{ $myob_transaction->amount }}</td>
                                    @if(isset($myob_transaction->user->customerAccounts[0]) && !empty($myob_transaction->user->customerAccounts[0]))
                                        <td>{{ "Account # {$myob_transaction->user->customerAccounts[0]->account_number}" }}</td>
                                    @else
                                        <td>No Account Info</td>
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
            $("#refunds-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>
@endsection
