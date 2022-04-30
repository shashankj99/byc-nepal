@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
    @if(auth()->user()->hasRole("Admin"))
        <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    @endif
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Location</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('location.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Location") }}
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
    @if(auth()->user()->hasRole("Admin"))
        <form action="{{ route('location') }}" method="GET">
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
                    <button type="submit" class="btn btn-dark btn-block">View User Location</button>
                </div>
            </div>
        </form>
    @endif
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="location-table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Address</th>
                                <th>Address Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $location["address"] }}</td>
                                    <td>{{ $location["type"] }}</td>
                                    <td>
                                        @if($location["is_default"] == "0")
                                            <a class="make-default badge badge-success" id="make-location-default"
                                               data-id="{{ $location['id'] }}">
                                                Make default
                                            </a>
                                        @else
                                            <small class="badge badge-dark">Default</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-link text-dark delete-location" data-id="{{ $location['id'] }}">
                                            <span class="fas fa-trash"></span>
                                        </a>
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
    @if(auth()->user()->hasRole("Admin"))
        <script src="{{ asset('js/select2.js') }}"></script>
    @endif
    <script>
        $(document).ready(function () {
            @if(auth()->user()->hasRole("Admin"))
                $(".user-select").select2();
            @endif
            $('#location-table').DataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            // make location default
            $('#location-table').on('click', '.make-default', function () {
               if (confirm("Do you want to make this address your default location?")) {
                   let id = this.dataset.id, defaultUrl = "{{ route('location.default', ':id') }}";
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

            // delete location
            $("#location-table").on("click", '.delete-location', function () {
                if (confirm("Are you sure you want to delete this location?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("location.delete", ":id") }}";
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
