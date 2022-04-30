@extends('layouts.app')

@section("page-styles")
    <style>
        .embed-responsive-21by9::before {
            padding-top: 0;
        }

        .testimonial-group > .row {
            overflow-x: auto;
            white-space: nowrap;
        }

        .testimonial-group > .row > .col {
            display: inline-block;
            float: none;
        }

        .center-block {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if(auth()->user()->hasRole("Admin"))
            @include("admin", [
                "total_active_customers" => $total_active_customers,
                "total_off_board_customers" => $total_off_board_customers,
                "total_orders" => $total_orders,
                "total_drivers" => $total_drivers,
                "year" => $year,
                "show_myob_div" => $show_myob_div
            ])
        @elseif(auth()->user()->hasRole("Driver"))
            @include("driver", ["user" => $user])
        @else
            @include("customer", ["customer" => $customer, "announcements" => $announcements, "current_balance" => $current_balance])
        @endif
    </div>
@endsection
