@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Assign User / Create Pickup</h1>
        </div>
    </div>
@endsection

@section("content")
    @if(session()->has("success"))
        <div class="row">
            <div class="col">
                @include("alerts.success", ["message" => session()->get("success")])
            </div>
        </div>
    @endif
    @if(session()->has("error"))
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => session()->get("error")])
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col">

            <div class="row mb-2">
                <div class="col-md-4 col-sm-12">
                    <label for="bin_number" class="form-label">Bin Number</label>
                    <input type="text" class="form-control" id="bin_number" readonly value="{{ $bin_info->bin_number }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 col-sm-12">
                    <label for="bin_type" class="form-label">Bin Type</label>
                    <input type="text" class="form-control" id="bin_type" readonly value="{{ $bin_info->bin_type }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 col-sm-12">
                    <label for="status" class="form-label">Status</label>
                    <input type="text" class="form-control" id="status" readonly value="{{ $bin_info->status }}">
                </div>
            </div>
            @if($bin_info->order)
                <div class="row mb-2">
                    <div class="col-md-4 col-sm-12">
                        <label for="customer_name" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer_name" readonly value="{{ $bin_info->order->user->full_name }}">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-sm-12">
                        <label for="customer_address" class="form-label">Customer Address</label>
                        <input type="text" class="form-control" id="customer_address" readonly value="{{ $bin_info->order->userAddress->address }}">
                    </div>
                </div>
                <div class="row mb-2 mt-2">
                    <div class="col-md-4 col-sm-12">
                        <a href="{{ route("driver.pickup.bin", $bin_info->id) }}" class="btn btn-dark">Pick Up Bin</a>
                    </div>
                </div>
            @else
                @if($bin_info->binUser)
                    <div class="row mb-2">
                        <div class="col-md-4 col-sm-12">
                            <label for="customer_name" class="form-label">Customer</label>
                            <input type="text" class="form-control" id="customer_name" readonly value="{{ $bin_info->order->user->full_name }}">
                        </div>
                    </div>
                @else
                    @if(isset($customers))
                        <form action="{{ route("user.bin.assign") }}" method="post">
                            @csrf
                            <input type="hidden" name="bin_id" value="{{ $bin_info->id }}">
                            <div class="row mb-2">
                                <div class="col-md-4 col-sm-12">
                                    <label for="user_id" class="form-label">Customer</label>
                                    <select name="user_id" id="user_id" class="form-control @error("user_id") is-invalid @enderror">
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 col-sm-12">
                                    <label for="user_address_id" class="form-label">Customer Address</label>
                                    <select name="user_address_id" id="user_address_id" class="form-control @error("user_address_id") is-invalid @enderror">
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 col-sm-12">
                                    <label for="order_id" class="form-label">Customer Orders</label>
                                    <select name="order_id" id="order_id" class="form-control @error("order_id") is-invalid @enderror">
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 col-sm-12">
                                    <button type="submit" class="btn btn-dark">Assign User</button>
                                </div>
                            </div>
                        </form>
                    @endif
                @endif
            @endif
        </div>
    </div>
@endsection

@section("page-scripts")
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".user_id").select2();

            const userDiv = $("#user_id");
            const userAddressDiv = $("#user_address_id");
            const orderDiv = $("#order_id");

            if (userDiv.find(":selected").val()) {

                let userId = userDiv.find(":selected").val(), defaultUrl = "{{ route('user.addresses', ":id") }}",
                    orderUrl = "{{ route("user.orders", ":id") }}";

                defaultUrl = defaultUrl.replace(":id", userId);
                orderUrl = orderUrl.replace(":id", userId);

                $.ajax({url: defaultUrl, method: "GET"})
                    .done(function (res) {
                        $.each(res.data, function (index, data) {
                            userAddressDiv.append(`<option value="${data.id}">${data.address}</option>`)
                        });
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });

                $.ajax({url: orderUrl, method: "GET"})
                    .done(function (res) {
                        $.each(res.data, function (index, data) {
                            orderDiv.append(`<option value="${data.id}">Order #${data.id} (${data.bin_type})</option>`)
                        });
                    })
                    .fail(function (xhr) {
                        alert(xhr.statusText);
                    });

            }

            userDiv.on("change", function () {
                let userId = $(this).find(":selected").val(), defaultUrl = "{{ route('user.addresses', ":id") }}",
                    orderUrl = "{{ route("user.orders", ":id") }}";

                if (userId) {

                    defaultUrl = defaultUrl.replace(":id", userId);
                    orderUrl = orderUrl.replace(":id", userId);

                    $.ajax({url: defaultUrl, method: "GET"})
                        .done(function (res) {
                            userAddressDiv.find("option").remove();
                            $.each(res.data, function (index, data) {
                                userAddressDiv.append(`<option value="${data.id}">${data.address}</option>`);
                            });
                        })
                        .fail(function (xhr) {
                            alert(xhr.statusText);
                        });

                    $.ajax({url: orderUrl, method: "GET"})
                        .done(function (res) {
                            orderDiv.find("option").remove();
                            $.each(res.data, function (index, data) {
                                orderDiv.append(`<option value="${data.id}">Order #${data.id} (${data.bin_type})</option>`)
                            });
                        })
                        .fail(function (xhr) {
                            alert(xhr.statusText);
                        });
                }
            });
        });
    </script>
@endsection
