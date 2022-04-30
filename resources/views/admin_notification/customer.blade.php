@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Notifications</h1>
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
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @foreach($notifications as $notification)
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10 mt-1">
                                <span>{{ $notification->description }}</span>
                            </div>
                            <div class="col-2">
                        <span class="float-right">
                            <a href="#" class="btn btn-link text-dark delete-notification" data-id="{{ $notification->id }}">
                                <i class="fas fa-trash"></i>
                            </a>
                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section("page-scripts")
    <script>
        $(".delete-notification").on("click", function () {
            if (confirm("Are you sure you want to delete this notification?")) {
                let id = this.dataset.id, defaultUrl = "{{ route("customer.admin.notification.delete", ":id") }}";
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
    </script>
@endsection
