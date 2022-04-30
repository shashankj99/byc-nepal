@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Charities</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('charity.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Charity") }}
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="charity-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Charity Name</th>
                                <th>Name Of Bank</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>BSB</th>
                                <th>Subscribed To</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($charities as $charity)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $charity->name }}</td>
                                    <td>{{ $charity->bank_name . ", " . $charity->branch }}</td>
                                    <td>{{ $charity->account_name }}</td>
                                    <td>{{ $charity->account_number }}</td>
                                    <td>{{ $charity->bsb }}</td>
                                    <td>{{ $charity->subscription->name }}</td>
                                    <td>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ route('charity.edit', $charity->id) }}"
                                               class="btn btn-link text-dark">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-link text-dark delete-charity"
                                               data-id="{{ $charity->id }}">
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
            $("#charity-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            $("#charity-table").on("click", '.delete-charity', function () {
                if (confirm("Are you sure you want to delete this charity?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("charity.delete", ":id") }}";
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
