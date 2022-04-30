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
</head>
<body>
<div class="mt-5">
    <div class="container">
        Dear Sir/Madam,
        <br>
        <br>
        Your account for BYC was successfully created by Admin. Below are your credentials:
        <br>
        <br>
        Username: {{ $email }}
        Password: Byc@1234
        <br>
        <br>
        Click on the link below to visit the BYC login page
        <br>
        <a href="{{ config("app.url") . "/login" }}" target="_blank">
            {{ config("app.url") . "/login" }}
        </a>
    </div>
</div>
</body>
</html>
