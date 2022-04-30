<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #abe3ab;
        }
        .center-block {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    @laravelPWA
</head>
<body>
{{--<div class="container mt-5">--}}
{{--    <div class="row">--}}
{{--        <div class="col-md-6 col-sm-12 col-xs-12 mb-3">--}}
{{--            <img src="{{ asset("/images/svg/Two Bins.svg") }}" alt="Dustbin" class="img-fluid">--}}
{{--        </div>--}}
{{--        <div class="col-md-6 col-sm-12 col-xs-12 mb-0">--}}
{{--            <div class="card mb-3">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-12 mb-3">--}}
{{--                            <h4 class="text-center">Enter you details</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="row">--}}
{{--                        <div class="col-12 mb-3">--}}
{{--                            <h5 class="text-center">Prompt or reliable home or business pick up</h5>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="row">--}}
{{--                        <div class="col-12 mb-3">--}}
{{--                            <h4 class="text-center">We'll take care of the rest</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="row">--}}
{{--                        <div class="col-12 mb-3">--}}
{{--                            <h5 class="text-center">Prompt and accurate payment. QR code on each bin for payment security.</h5>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            @if(auth()->guest())--}}
{{--                <div class="d-flex justify-content-evenly mt-3">--}}
{{--                    <a class="btn btn-lg btn-dark btn-block" href="{{ route('register') }}">--}}
{{--                        {{ __('Sign Up') }}--}}
{{--                    </a>--}}
{{--                    <a class="btn btn-lg btn-light btn-block" href="{{ route('login') }}">--}}
{{--                        {{ __('Sign In') }}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            @else--}}
{{--                <div class="d-flex justify-content-evenly mt-3">--}}
{{--                    <a class="btn btn-lg btn-dark btn-block" href="{{ route('dashboard') }}">--}}
{{--                        {{ __('Visit Dashboard') }}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="container">
    <div class="d-flex justify-content-center flex-column">
        <img src="{{ asset("/images/img/byc-dustbin.png") }}" alt="Dustbin" class="center-block img-fluid" style="max-height: 620px">
        <h4 class="text-center text-sm mt-5">Sign Up Or Sign In</h4>
        <h5 class="text-center mt-3 text-dark">
            Prompt and reliable Home and Commercial pick up service for bottles and cans.
        </h5>
        <h5 class="text-center mt-3 text-dark">
            Prompt and accurate refunds - each bin has a unique QR code for payment security.
        </h5>
        @if(auth()->guest())
            <div class="d-flex justify-content-evenly mt-5 mb-3">
                <a class="btn btn-lg btn-dark btn-block" href="{{ route('register') }}">
                    {{ __('Sign Up') }}
                </a>
                <a class="btn btn-lg btn-light btn-block" href="{{ route('login') }}">
                    {{ __('Sign In') }}
                </a>
            </div>
        @else
            <div class="d-flex justify-content-evenly mt-5 mb-3">
                <a class="btn btn-lg btn-dark btn-block" href="{{ route('dashboard') }}">
                    {{ __('Visit Dashboard') }}
                </a>
            </div>
        @endif
    </div>
</div>
</body>
</html>
