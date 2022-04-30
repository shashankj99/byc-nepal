@extends("layouts.app")

@section("page-styles")
    <style>
        @media print {
            #printJS-div {
                width: 1000px !important;
            }
        }
    </style>
@endsection

@section("content-header")
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12 col-xs-12 mb-2">
            <h1 class="m-0">Bin Management</h1>
        </div>
        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <button class="btn btn-dark btn-block" onclick="printDiv()">Print QR Code</button>
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
    @if(session()->has("success"))
        <div class="row">
            <div class="col">
                @include("alerts.success", ["message" => session()->get("success")])
            </div>
        </div>
    @endif
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route("bin") }}" class="btn btn-link text-dark">List View</a> /
            <a href="{{ route("bin.print") }}" class="btn btn-link text-dark">Print View</a>
        </div>
    </div>
    <div class="row mb-3" id="printJS-div">
        @foreach($bins as $bin)
            <div class="col-md-6 col-sm-12 mb-3">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img src="{{ "https://chart.googleapis.com/chart?cht=qr&chs=540x540&chl=$url{$bin->qr_code}" }}"
                         alt="{{ $bin->qr_code }}" class="img-fluid" style="max-width: 540px; max-height: 540px">
                    <h1>{{ "{$bin->bin_number}" }}</h1>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="d-flex justify-content-center">
                {!! $bins->links() !!}
            </div>
        </div>
    </div>
@endsection

@section("page-scripts")
    <script>
        function printDiv()
        {
            const divToPrint=document.getElementById('printJS-div');

            const newWin=window.open('','Print-Window');

            newWin.document.open();

            newWin.document.write(
                `<html>
                    <head>
                        <style>
                            @media print {
                                #printJS-div {
                                    width: 1000px !important;
                                }
                            }
                            * {
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body onload="window.print();window.close()">${divToPrint.innerHTML}
                    </body>
                </html>`
            );

            newWin.document.close();

        }
    </script>
@endsection
