@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('announcement') }}" class="btn btn-dark">
                {{ __("View Announcements") }}
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
    <form action="{{ route('announcement.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{ method_field("PUT") }}
        @include("announcement._form", ["announcement" => $announcement, "buttonText" => "Update"])
    </form>
@endsection

