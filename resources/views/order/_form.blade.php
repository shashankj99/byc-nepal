<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="user-id">Select A Customer</label>
        <select name="user_id" id="user-id"
                class="form-control user-id @error("user_id") is-invalid @enderror" required>
            @foreach($users as $user)
                <option value="{{ $user["id"] }}"
                        @if((isset($order) && ($order->user_id == $user["id"])) || (old("user_id") == $user["id"])) selected @endif>
                    {{ $user["full_name"] }}
                </option>
            @endforeach
        </select>
        @error('user_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="user-address-id">Select Customer's Address</label>
        <select name="user_address_id" id="user-address-id"
                class="form-control user-address-id @error("user_address_id") is-invalid @enderror" required>
        </select>
        @error('user_address_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="subscription-id">Select A Subscription</label>
        <select name="subscription_id" id="subscription-id"
                class="form-control subscription-id @error("subscription_id") is-invalid @enderror" required>
            @foreach($subscriptions as $subscription)
                <option value="{{ $subscription->id }}" data-name="{{ $subscription->name }}"
                        @if((isset($order) && ($order->subscription_id == $subscription->id)) || (old("subscription_id") == $subscription->id)) selected @endif>
                    {{ $subscription->name }}
                </option>
            @endforeach
        </select>
        @error('subscription_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3 charity-div">
    <div class="col-md-4 col-sm-12">
        <label for="charity">Select A Charity</label>
        <select name="charity" id="charity"
                class="form-control charity-id @error("charity_id") is-invalid @enderror">
            <option value="">-- Click To View Charities --</option>
            @foreach($charities as $charity)
                <option value="{{ $charity->name }}"
                        @if((isset($order) && ($order->charity == $charity->name)) || (old("charity") == $charity->name)) selected @endif>
                    {{ $charity->name }}
                </option>
            @endforeach
        </select>
        @error('charity')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="bin-type" class="form-label">Bin Type</label>
        <select name="bin_type" id="bin-type" class="form-control @error("bin_type") is-invalid @enderror"
                required>
            <option value="drum-bin"
                    @if((isset($order) && $order->bin_type == "Drum Bin") || (old("bin_type") == "drum-bin")) selected @endif>
                200 litre drum bin
            </option>
            <option value="wheelie-bin"
                    @if((isset($order) && $order->bin_type == "Wheelie Bin") || (old("bin_type") == "wheelie-bin")) selected @endif>
                240 litre wheelie bin
            </option>
        </select>
        @error("bin_type")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3 payment-type-div">
    <div class="col-md-4 col-sm-12">
        <label for="payment-type" class="form-label">Payment Type</label>
        <select name="payment_type" id="payment-type" class="form-control"
                required>
            <option value="full">One Time $40 Payment</option>
            <option value="installment">Deduct From Payout (4 x $10)</option>
        </select>
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="amount" class="form-label">Amount</label>
        <input type="text" class="form-control @error("amount") is-invalid @enderror" name="amount" id="amount"
               value="@isset($order) {{ $order->amount }} @else {{ old("amount") }} @endisset" placeholder="0.00"
               required>
        @error("amount")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="order-status" class="form-label">Order Status</label>
        <select name="order_status" id="order-status" class="form-control @error("order_status") is-invalid @enderror"
                required>
            <option value="pending"
                    @if((isset($order) && $order->order_status == "pending") || (old("order_status") == "pending")) selected @endif>
                Pending
            </option>
            <option value="accepted"
                    @if((isset($order) && $order->order_status == "accepted") || (old("order_status") == "accepted")) selected @endif>
                Accepted
            </option>
            <option value="rejected"
                    @if((isset($order) && $order->order_status == "rejected") || (old("order_status") == "rejected")) selected @endif>
                Rejected
            </option>
        </select>
        @error("order_status")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="form-group row mb-3">
    <div class="col-md-4 col-sm-12">
        <label for="payment-status" class="form-label">Payment Status</label>
        <select name="payment_status" id="payment-status"
                class="form-control @error("payment_status") is-invalid @enderror"
                required>
            <option value="complete"
                    @if((isset($order) && $order->payment_status == "complete") || (old("payment_status") == "complete")) selected @endif>
                Complete
            </option>
            <option value="incomplete"
                    @if((isset($order) && $order->payment_status == "incomplete") || (old("payment_status") == "incomplete")) selected @endif>
                Incomplete
            </option>
        </select>
        @error("payment_status")
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <button type="submit" class="btn btn-dark btn-block">
            {{ $buttonText }}
        </button>
    </div>
</div>
