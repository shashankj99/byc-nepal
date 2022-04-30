@extends("layouts.app")

@section("page-styles")
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('charity') }}" class="btn btn-dark">
                {{ __("View charities") }}
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
    <form action="{{ route('charity') }}" method="POST">
        @csrf
        @include("charity._form", ["buttonText" => "Submit", "subscriptions" => $subscriptions])
    </form>
@endsection

@section("page-scripts")
    <script src="{{ asset('js/select2.js') }}"></script>
    <script>
        $(".subscription-select").select2();
    </script>
@endsection
