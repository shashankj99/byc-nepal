@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('pickup') }}" class="btn btn-dark">
                {{ __("View Pickup Orders") }}
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
    <form action="{{ route("pickup.update", $pickup->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        @include("pickup._form", ["pickup" => $pickup, "users" => $users, "buttonText" => "Update"])
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(document).ready(function () {
            const userIdSelect = $(".user-id");
            const userAddressId = $("#user-address-id");
            const userAddressIdFromPickup = "{{ $pickup->user_address_id }}";

            userIdSelect.select2();
            $(".user-address-id").select2();

            let userId = userIdSelect.find(":selected").val();

            if (userId) getUserAddresses(userId);

            userIdSelect.on("change", function () {
                userId = $(this).find(":selected").val();
                getUserAddresses(userId);
            });

            function getUserAddresses(userId)
            {
                let addressUrl = "{{ route("customer.address", ":id") }}";
                addressUrl = addressUrl.replace(":id", userId);
                $.get(addressUrl)
                    .done(function (response) {
                        userAddressId.empty();
                        response.data.forEach(function (res) {
                            if (userAddressIdFromPickup && parseInt(userAddressIdFromPickup) === res.id)
                                userAddressId.append($("<option />").val(res.id).text(res.address).attr("selected", "selected"));
                            else
                                userAddressId.append($("<option />").val(res.id).text(res.address));
                        })
                    })
                    .fail(err => alert(err.statusText));
            }
        });
    </script>
@endsection
