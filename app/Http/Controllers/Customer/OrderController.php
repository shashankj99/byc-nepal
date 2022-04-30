<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerSubscription;
use App\Models\Order;
use App\Models\PreOrder;
use App\Models\User;
use App\Models\UserCharity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * view order bin page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user = User::with("customerSubscriptions", "preOrders")
                ->select("id")
                ->where("id", "=", Auth::id())
                ->first();

            $latest_subscription = $user->customerSubscriptions()->latest("id")
                ->first();

            if(!$latest_subscription) return redirect()->route("customer.subscription");

            if ($latest_subscription->subscription->name == "Charity") {
                $user_charity = UserCharity::where("user_id", "=", Auth::id())
                    ->select("id")
                    ->latest("id")
                    ->first();

                if (!$user_charity) return redirect()->route("customer.charity");
            } else $user_charity = null;

            if ($latest_subscription->has_pre_order == "1") {
                if ($user->preOrders->count() > 0) {
                    $pre_order = $user->preOrders->toArray()[0];

                    if ($pre_order["bin_type"] == "wheelie-bin") {
                        if ($pre_order["payment_type"] == null)
                            return redirect()->route("customer.payment.type", ["pre_order_id" => $pre_order["id"]]);
                        else return redirect()->route("customer.order.confirm");
                    }

                    if ($pre_order["bin_type"] == "drum-bin" && $pre_order["status"] == "incomplete")
                        return redirect()->route("customer.order.confirm");

                    if ($pre_order["status"] == "complete") return redirect()->route("customer.subscription");
                }
            }

            return view("order.customer.customer")
                ->with([
                    "latest_subscription_id" => $latest_subscription->subscription_id,
                    "user_charity" => $user_charity
                ]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add pre-order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "bin_type" => "required|in:drum-bin,wheelie-bin",
                "subscription_id" => "required|integer|min:1"
            ]);

            $request_body["user_id"] = Auth::id();
            $request_body["bin_type"] = $request->bin_type;
            $request_body["subscription_id"] = $request->subscription_id;
            $request_body["charity_id"] = $request->charity_id;
            $request_body["payment_type"] = ($request_body["bin_type"] == "drum_bin") ? "full" : null;

            DB::beginTransaction();

            $pre_order = PreOrder::create($request_body);

            $customer_subscription = CustomerSubscription::where("user_id", "=", $request_body["user_id"])
                ->where("subscription_id", "=", $request_body["subscription_id"])
                ->where("has_pre_order", "=", "0")
                ->latest("id")
                ->firstOrFail();

            $customer_subscription->update(["has_pre_order" => "1"]);

            if ($customer_subscription->subscription->name == "Charity" && isset($request_body["charity_id"])) {
                $user_charity = UserCharity::where("user_id", "=", $request_body["user_id"])
                    ->where("id", "=", $request_body["charity_id"])
                    ->where("has_pre_order", "=", "0")
                    ->latest("id")
                    ->firstOrFail();

                $user_charity->update(["has_pre_order" => "1"]);
            }

            DB::commit();

            if ($request_body["bin_type"] == "drum-bin")
                return redirect()->route("customer.order.bin");

            return redirect()->route("customer.payment.type", ["pre_order_id" => $pre_order->id]);
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (ModelNotFoundException $modelNotFoundException) {
            DB::rollBack();
            Session::flash("error", "Customer is not associated to any subscription");
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function confirmOrder()
    {
        try {
            $pre_order = PreOrder::with(["subscription", "user" => function ($q) {
                $q->with(["customerAccounts" => function ($q) {
                    $q->where("is_default", "=", "1");
                }]);
            }])
                ->where("user_id", "=", Auth::id())
                ->where("status", "=", "incomplete")
                ->latest("id")
                ->firstOrFail();

            return view("order.customer.confirm")
                ->with(["pre_order" => $pre_order]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to fetch the order details");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View edit order page
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editOrderPage($id)
    {
        try {
            $pre_order = PreOrder::with("subscription")
                ->where("user_id", "=", Auth::id())
                ->where("status", "=", "incomplete")
                ->latest("id")
                ->findOrFail($id);

            return view("order.customer.edit")
                ->with(["pre_order" => $pre_order]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to fetch the order details");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update order data
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderData($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "bin_type" => "required|in:drum-bin,wheelie-bin",
            ]);

            $pre_order = PreOrder::where("user_id", "=", Auth::id())
                ->where("status", "=", "incomplete")
                ->latest("id")
                ->findOrFail($id);

            $pre_order->update(["bin_type" => $request->bin_type]);

            if ($pre_order->bin_type == "wheelie-bin")
                return redirect()->route("customer.payment.type.show", $id);

            return redirect()->route("customer.order.confirm");
        } catch (ModelNotFoundException $modelNotFoundException) {
            DB::rollBack();
            Session::flash("error", "Unable to fetch the order details");
            return redirect()->back();
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View order history for customers
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function viewOrderHistoryPage()
    {
        try {
            $orders = Order::with("subscription")
                ->where("user_id", "=", Auth::id())
                ->get();
            return view("order.customer.index")
                ->with(["orders" => $orders, "i" => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
