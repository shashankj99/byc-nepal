@if(session()->has("error"))
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @include("alerts.error", ["message" => session()->get("error")])
        </div>
    </div>
@endif
@if(session()->has("info"))
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @include("alerts.info", ["message" => session()->get("info")])
        </div>
    </div>
@endif
@if(session()->has("success"))
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @include("alerts.success", ["message" => session()->get("success")])
        </div>
    </div>
@endif
@if(session()->has("warning"))
    <div class="row">
        <div class="col-md-4 col-sm-12">
            @include("alerts.warning", ["message" => session()->get("warning")])
        </div>
    </div>
@endif
<div class="row">
    <div class="col">
        <div class="embed-responsive embed-responsive-21by9">
            <iframe style="width: 100%; height: 100vh; position: relative;" class="embed-responsive-item" src="{{ $user->driver->route }}"></iframe>
        </div>
    </div>
</div>
