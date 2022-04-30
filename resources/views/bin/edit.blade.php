@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('bin') }}" class="btn btn-dark">
                {{ __("View All Bins") }}
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
    <form action="{{ route('bin.update', $bin->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        @include("bin._form", ["bin" => $bin, "buttonText" => "Update"])
    </form>
@endsection

