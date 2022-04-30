@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset("css/datatables.css") }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Subscriptions</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <a href="{{ route('subscription.create') }}" class="btn btn-block btn-dark">
                {{ __("Add New Subscription") }}
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
                        <table class="table table-striped table-sm" id="subscription-table">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Subscription Type</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $subscription->name }}</td>
                                    <td>{{ $subscription->description }}</td>
                                    <td>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ route('subscription.edit', $subscription->id) }}"
                                               class="btn btn-link text-dark">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-link text-dark delete-subscription"
                                               data-id="{{ $subscription->id }}">
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
            $("#subscription-table").dataTable({
                "language": {
                    "info": "Showing _END_ of _TOTAL_ entries"
                }
            });

            $("#subscription-table").on("click", '.delete-subscription', function () {
                if (confirm("Are you sure you want to delete this subscription?")) {
                    let id = this.dataset.id, defaultUrl = "{{ route("subscription.delete", ":id") }}";
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
