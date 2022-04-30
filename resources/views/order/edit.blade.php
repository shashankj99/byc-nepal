@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('orders') }}" class="btn btn-dark">
                {{ __("View Bin Orders") }}
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
    <form action="{{ route("order.update", $order->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        @include("order._form", ["order" => $order, "charities" => $charities, "subscription" => $subscriptions, "users" => $users, "buttonText" => "Update"])
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".user-id").select2();
            $(".subscription-id").select2();
            $(".charity-id").select2();

            const userDiv = $("#user-id");
            const subscriptionId = $("#subscription-id");
            const charityDiv = $(".charity-div");
            const binType = $("#bin-type");
            const paymentTypeDiv = $(".payment-type-div");
            const userAddressDiv = $("#user-address-id");
            const userAddressId = "{{ $order->user_address_id }}";

            if (userDiv.find(":selected").val()) {

                let userId = userDiv.find(":selected").val(), defaultUrl = "{{ route('user.addresses', ":id") }}";

                defaultUrl = defaultUrl.replace(":id", userId);

                $.ajax({url: defaultUrl, method: "GET"})
                    .done(function (res) {
                        $.each(res.data, function (index, data) {
                            userAddressDiv.append(`<option value="${data.id}">${data.address}</option>`);
                        });

                        userAddressDiv.find("option").each(function () {
                            if($(this).val() === userAddressId)
                                $(this).attr("selected", "selected");
                        });
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });

            }

            if (subscriptionId.find(":selected").attr("data-name") === "Charity")
                charityDiv.show();
            else
                charityDiv.hide();

            if (binType.find(":selected").val() === "wheelie-bin")
                paymentTypeDiv.show();
            else
                paymentTypeDiv.hide();

            userDiv.on("change", function () {
                let userId = $(this).find(":selected").val(), defaultUrl = "{{ route('user.addresses', ":id") }}";

                if (userId) {

                    defaultUrl = defaultUrl.replace(":id", userId);

                    $.ajax({url: defaultUrl, method: "GET"})
                        .done(function (res) {
                            $.each(res.data, function (index, data) {
                                userAddressDiv.append(`<option value="${data.id}" selected=${selected}>${data.address}</option>`)
                            });
                        })
                        .fail(function (xhr) {
                            alert(xhr.statusText);
                        });

                }
            });

            subscriptionId.on("change", function () {
                if($(this).find(":selected").attr("data-name") === "Charity")
                    charityDiv.show();
                else
                    charityDiv.hide();
            });

            binType.on("change", function () {
                if($(this).find(":selected").val() === "wheelie-bin")
                    paymentTypeDiv.show();
                else
                    paymentTypeDiv.hide();
            });
        });
    </script>
@endsection
