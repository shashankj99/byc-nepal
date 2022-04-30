@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Announcements</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('announcement.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Announcement") }}
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
                        <table class="table table-striped table-sm" id="announcement-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Image</th>
                                <th>Heading</th>
                                <th>Sub-Head</th>
                                <th>Description</th>
                                <th>Publish From</th>
                                <th>Publish To</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($announcements as $announcement)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        @if($announcement->image)
                                            <div class="product-img">
                                                <img src="{{ asset("/images/announcements/{$announcement->image}") }}"
                                                     alt="Announcement-Image"
                                                     class="img-size-64" height="50" width="80">
                                            </div>
                                        @else
                                            <div class="product-img">
                                                <img src="{{ asset("/images/svg/Green-Bulb.svg") }}"
                                                     alt="Announcement-Image"
                                                     class="img-size-64" height="50" width="80">
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $announcement->heading }}</td>
                                    <td>{{ $announcement->sub_heading }}</td>
                                    <td>{{ $announcement->description }}</td>
                                    <td>{{ $announcement->publish_from_formatted }}</td>
                                    <td>{{ $announcement->publish_to_formatted }}</td>
                                    <td>
                                        @if($announcement->status == "active")
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ route('announcement.edit', $announcement->id) }}"
                                               class="btn btn-link text-dark">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-link text-dark delete-announcement"
                                               data-id="{{ $announcement->id }}">
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
            $("#announcement-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            $("#announcement-table").on("click", '.delete-announcement', function () {
                if (confirm("Are you sure you want to delete this announcement?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("announcement.delete", ":id") }}";
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
