@if(session()->has("error"))
    <div class="row">
        <div class="col">
            @include("alerts.error", ["message" => session()->get("error")])
        </div>
    </div>
@endif
@if(session()->has("info"))
    <div class="row">
        <div class="col">
            @include("alerts.info", ["message" => session()->get("info")])
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
@if(session()->has("warning"))
    <div class="row">
        <div class="col">
            @include("alerts.warning", ["message" => session()->get("warning")])
        </div>
    </div>
@endif
<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Your current bin drop-off / Pick-Up Location is </h6>
                <span class="text-bold">{{ $customer->userAddresses[0]["address"] }}</span>
            </div>
        </div>
    </div>
</div>
<div class="testimonial-group">
    <div class="row mb-3">
        <div class="col">
            <div class="d-flex">
                <a href="{{ route("customer.order.bin") }}" class="text-dark">
                    <div class="card mr-2" style="height: calc(100% - 16px);">
                        <div class="card-body" style="padding: 10px 20px">
                            <div class="d-flex flex-column">
                                <img src="{{ asset('images/svg/Order-a-Bin.svg') }}" alt=""
                                     class="direct-chat-img center-block">
                                <span class="text-center">Order a bin</span>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route("customer.pickup") }}" class="text-dark">
                    <div class="card mr-2" style="height: calc(100% - 16px);">
                        <div class="card-body" style="padding: 10px 20px">
                            <div class="d-flex flex-column justify-content-evenly">
                                <img src="{{ asset('images/svg/Request-a-pick-up.svg') }}" alt=""
                                     class="direct-chat-img center-block">
                                <span class="text-center">Request a <br /> pickup</span>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="{{ route("customer.notification") }}" class="text-dark">
                    <div class="card" style="height: calc(100% - 16px);">
                        <div class="card-body" style="padding: 10px 20px">
                            <div class="d-flex flex-column justify-content-evenly">
                                <img src="{{ asset('images/svg/Notify-us-of-changes.svg') }}" alt=""
                                     class="direct-chat-img center-block">
                                <span class="text-center">Notify us <br /> of changes</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column justify-content-center">
                    <h3 class="text-bold text-center mb-2">$ {{ $current_balance }}</h3>
                    <span class="text-info text-center mb-3">Personal Refund Balance</span>
                    <a href="{{ route("customer.refunds") }}" class="btn btn-dark btn-block">
                        View all refunds
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@foreach($announcements as $announcement)
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5><strong>{{ $announcement->heading }}</strong></h5>
                                    @if($announcement->sub_heading)
                                        <span class="text-muted">{{ $announcement->sub_heading }}</span><br>
                                    @endif
                                    <span class="text-wrap text-sm">
                                        {{ $announcement->description }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center justify-content-center">
                            @if($announcement->image)
                                <img class="img-fluid float-right"
                                     src="{{ asset("images/announcements/{$announcement->image}") }}"
                                     alt="" style="max-height: 120px">
                            @else
                                <img class="img-fluid" src="{{ asset('images/svg/Green-Bulb.svg') }}" alt=""
                                     style="max-height: 120px">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
