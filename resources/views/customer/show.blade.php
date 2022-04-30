@extends("layouts.app")

@if(auth()->user()->hasRole("Admin"))
@section("content-header")
    <div class="row mb-3">
        <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
            <a href="{{ route('customer') }}" class="btn btn-dark">
                {{ __("View all customers") }}
            </a>
        </div>
    </div>
@endsection
@endif

@section("content")
    @if(session()->has("error"))
        <div class="row">
            <div class="col">
                @include("alerts.error", ["message" => session()->get("error")])
            </div>
        </div>
    @endif
    @if(auth()->user()->hasRole("Admin"))
        @php($route = route("customer.update", $customer->id))
    @else
        @php($route = route("user.update", $customer->id))
    @endif
    <div class="row">
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Details</h3>
                </div>
                <form action="{{ $route }}" method="POST">
                    @csrf
                    {{ method_field("PUT") }}
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error("first_name") is-invalid @enderror"
                                       value="{{ $customer->first_name }}" id="first_name" name="first_name">
                                @error("first_name")
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error("last_name") is-invalid @enderror"
                                       value="{{ $customer->last_name }}" id="last_name" name="last_name">
                                @error("last_name")
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            +61
                                        </div>
                                    </div>
                                    <input type="text" class="form-control @error("mobile_number") is-invalid @enderror"
                                           value="{{ $customer->mobile_number }}" id="mobile_number"
                                           name="mobile_number">
                                    @error("mobile_number")
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error("email") is-invalid @enderror"
                                       value="{{ $customer->email }}" id="email" name="email">
                                @error("email")
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error("password") is-invalid @enderror">
                                @error("password")
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control @error("password_confirmation") is-invalid @enderror">
                                @error("password_confirmation")
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if(auth()->user()->hasRole("Admin"))
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status"
                                            class="form-control @error("status") is-invalid @enderror">
                                        <option value="active" @if($customer->status == "active") selected @endif>Active
                                        </option>
                                        <option value="inactive" @if($customer->status == "inactive") selected @endif>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="off_board_at" class="form-label">Off Board Date</label>
                                    <input type="date" id="off_board_at" name="off_board_at"
                                           class="form-control @error("off_board_at") is-invalid @enderror"
                                           value="{{ $customer->off_board_at }}">
                                    @error("off_board_at")
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="myob_uid" class="form-label">Myob UID</label>
                                    <input type="text" class="form-control @error("myob_uid") is-invalid @enderror"
                                           id="myob_uid" name="myob_uid"
                                           value="@isset($customer){{ $customer->myob_uid }}@endisset">
                                    @error("myob_uid")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col">
                                <button type="submit" class="btn btn-dark btn-block">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if(!$customer->hasRole("Driver"))
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Subscriptions</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table-bordered table-sm" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Bin Number</th>
                                    <th>QR Code</th>
                                    <th>Subscription</th>
                                    <th>Bin Type</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($customer->bins as $bin)
                                    <tr>
                                        <td>{{ $bin->bin_number }}</td>
                                        <td>{{ $bin->qr_code }}</td>
                                        <td>{{ $bin->order->subscription->name }}</td>
                                        @if($bin->bin_type == "drum-bin")
                                            <td>200 L drum bin</td>
                                        @else
                                            <td>240 L wheelie bin</td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(auth()->user()->hasRole("Admin"))
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Transaction</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table-bordered table-sm" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Account</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($customer->myobTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->payment_date }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        @isset($customer->customerAccounts[0])
                                            <td>Account #{{ $customer->customerAccounts[0]->account_number }}</td>
                                        @else
                                            <td>No Default Account Selected</td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
