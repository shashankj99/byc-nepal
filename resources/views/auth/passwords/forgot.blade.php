@extends('layouts.app')

@section('login-content')
    @include("auth.passwords._form", ["route" => route("customer.forgot.password"), "token" => $token])
@endsection
