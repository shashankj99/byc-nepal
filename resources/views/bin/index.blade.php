@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    <link rel="stylesheet" href="{{ asset("css/datatables.button.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Bin Management</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('bin.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Bin") }}
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
            <a href="{{ route("bin") }}" class="btn btn-link text-dark">List View</a> /
            <a href="{{ route("bin.print") }}" class="btn btn-link text-dark">Print View</a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="bin-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Bin No.</th>
                                <th>QR Code</th>
                                <th>Bin Type</th>
                                <th>Status</th>
                                <th>Decommission Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bins as $bin)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $bin->order_id }}</td>
                                    <td>{{ $bin->bin_number }}</td>
                                    <td>
                                        {{ $bin->qr_code }}
                                    </td>
                                    <td>{{ $bin->bin_type_formatted }}</td>
                                    <td>
                                        @if($bin->status == "allocated")
                                            <span class="badge badge-pill badge-success">Allocated</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">Unallocated</span>
                                        @endif
                                    </td>
                                    <td>{{ $bin->decomposition_date }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link text-dark dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a href="{{ route('bin.edit', $bin->id) }}" class="dropdown-item">
                                                Edit Bin
                                            </a>
                                            <a href="{{ "https://chart.googleapis.com/chart?cht=qr&chs=540x540&chl=$url{$bin->qr_code}&title=BYC-Bin-{$bin->bin_number}" }}"
                                               class="dropdown-item"
                                               target="_blank"
                                            >
                                                View QR Code
                                            </a>
                                            <a href="#" data-id="{{ $bin->id }}" class="dropdown-item decompose-bin">
                                                Decommission Bin
                                            </a>
                                            <a href="#" data-id="{{ $bin->id }}" class="dropdown-item delete-bin">
                                                Delete Bin
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
    <script src="{{ asset("js/datatables.button.js") }}"></script>
    <script src="{{ asset("js/datatables.jszip.js") }}"></script>
    <script src="{{ asset("js/datatables.button.html5.js") }}"></script>
    <script>
        $(document).ready(function () {
            $("#bin-table").dataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excel",
                        title: "Bins",
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

            $("#bin-table").on("click", '.delete-bin', function () {
                if (confirm("Are you sure you want to delete this bin?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("bin.delete", ":id") }}";
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

            $("#bin-table").on("click", '.decompose-bin', function () {
                if (confirm("Are you sure you want to decommission this bin?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("bin.decompose", ":id") }}";
                    defaultUrl = defaultUrl.replace(":id", id);
                    $.ajax({
                        url: defaultUrl,
                        method: "GET",
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
