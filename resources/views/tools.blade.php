@extends("layouts.app")

@section("content-header")
    <div class="row mb-3">
        <div class="col">
            <h1 class="m-0">Settings & Tools</h1>
        </div>
    </div>
@endsection

@section("content")
    @if(session()->has("success"))
        <div class="row">
            <div class="col">
                @include("alerts.success", ["message" => session()->get("success")])
            </div>
        </div>
    @endif
    @if(session()->has("error"))
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => session()->get("error")])
            </div>
        </div>
    @endif
    @if(session()->has("info"))
        <div class="row">
            <div class="col">
                @include("alerts.info", ["message" => session()->get("info")])
            </div>
        </div>
    @endif
    <div class="row mb-2">
        <div class="col-md-6 col-sm-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sync App Customers with MYOB Supplier Card Files</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <span>
                                <strong>
                                    This process fetches the MYOB Supplier UIDs required to connect the App customer with their MYOB Card File. Simply add the user id (eg. 437) to the Notes field of the MYOB Supplier form and press the ‘Save' button.
                                </strong>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            @if(isset($sync_customers) && $sync_customers->status == "pending")
                                <button disabled class="btn btn-dark">Syncing...</button>
                            @else
                                <a href="{{ route("myob.supplier") }}" class="btn btn-dark">Sync Users</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sync Refunds with Customers</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <span>
                                <strong>
                                    This button will sync all the refunds for an individual user. NB: Always sync users first before syncing their refunds.. If the user's MYOB UID in not in our database the syncing process won't work.
                                </strong>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            @if(isset($sync_myob_transactions) && $sync_myob_transactions->status == "pending")
                                <button disabled class="btn btn-dark">Syncing...</button>
                            @else
                                <a href="{{ route("myob.supplier.refunds") }}" class="btn btn-dark">Sync User Refunds</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6 col-sm-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MYOB Search UID</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <span>
                                <strong>
                                    Only use this function if an error occurs during syncing. If a customer record fails to sync with the MYOB UID, you can search on the user's name to find it. NB: This isn’t a background process so it may take a while to fetch the data.
                                </strong>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <form action="{{ route("tools") }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" name="myob_username" id="myob_username"
                                                   class="form-control" placeholder="Enter User's Full Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2 col-sm-12">
                                        <button type="submit" class="btn btn-block btn-dark">
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(isset($uid) && $uid != "")
                        <div class="row mb-2">
                            <div class="col">
                                <span>
                                    <strong>Myob UID: </strong>
                                    {{ $uid }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Import Customers & Bins
                    </h3>
                </div>
                <div class="card-body">
                    <span>
                        <strong>
                            Download a Sample Sheet and insert new Bin or Customer data into the pre-defined rows. Please clear the sample data before adding new data (as this is just provided for guidance). Using alternative spreadsheets may result in errors.
                        </strong>
                    </span>
                    <div class="row mt-3">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <a href="{{ asset("files/Customer.csv") }}">Download Sample Sheet (Customer)</a>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-2">
                            <a href="{{ asset("files/Bin.csv") }}" class="float-right">Download Sample Sheet (Bin)</a>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route("import.customer") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="customer_sheet" class="form-label">Upload Customer Sheet</label>
                                    <input type="file" name="customer_sheet" id="customer_sheet"
                                           class="form-control @error("customer_sheet") is-invalid @enderror">
                                    @error("customer_sheet")
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2 col-sm-12">
                                @if(isset($import_user_flag) && $import_user_flag->status == "pending")
                                    <button disabled class="btn btn-block btn-dark">
                                        Importing...
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-block btn-dark">
                                        Submit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                    <form action="{{ route("import.bin") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="bin_sheet" class="form-label">Upload Bin Sheet</label>
                                    <input type="file" name="bin_sheet" id="bin_sheet"
                                           class="form-control @error("bin_sheet") is-invalid @enderror">
                                    @error("bin_sheet")
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                @if(isset($import_bin_flag) && $import_bin_flag->status == "pending")
                                    <button disabled class="btn btn-block btn-dark">
                                        Importing...
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-block btn-dark">
                                        Submit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
