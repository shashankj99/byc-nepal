@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('charity') }}" class="btn btn-dark">
                {{ __("View Charities") }}
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
    <form action="{{ route('charity.update', $charity->id) }}" method="POST">
        @csrf
        {{ method_field("PUT") }}
        @include("charity._form", ["charity" => $charity, "buttonText" => "Update", "subscriptions" => $subscriptions])
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(".subscription-select").select2();
    </script>
@endsection

