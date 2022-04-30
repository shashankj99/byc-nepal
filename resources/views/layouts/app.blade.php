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
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset("css/admin.css") }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset("css/byc.css") }}">

    @yield("page-styles")

    @laravelPWA
</head>
<body
    class="hold-transition {{ (request()->is('login') || request()->is('register') || request()->is('password/reset') || request()->is("email/verify") || request()->is("password/change") || request()->is("reset/password")) ? 'login-page' : 'sidebar-mini layout-fixed layout-navbar-fixed' }} text-sm">
@if (request()->is('login') || request()->is('register') || request()->is('password/reset') || request()->is("email/verify") || request()->is("password/change") || request()->is("reset/password"))
    @yield('login-content')
@else
    <div class="wrapper">

        @include('layouts._navbar')
        @include('layouts._sidebar')

        <div class="content-wrapper">
            <div id="overlay">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <section class="content-header">
                <div class="container-fluid mt-3">
                    @yield('content-header')
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

    </div>
@endif
<script src="{{ asset("js/admin.js") }}"></script>
@yield("page-scripts")
<script>
    $(window).on("load", function () {
        $("#overlay").fadeOut();
    });
</script>
</body>
</html>
