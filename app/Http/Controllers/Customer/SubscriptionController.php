<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerSubscription;
use App\Models\PreOrder;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * View subscriptions for customers
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $subscriptions = Subscription::all();
            return view("subscription.customer")
                ->with(["subscriptions" => $subscriptions]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Store customer subscription
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["subscription_id" => "required|integer|min:1"]);

            $request_body["subscription_id"] = $request->subscription_id;
            $request_body["user_id"] = Auth::id();

            $customer_subscription = CustomerSubscription::create($request_body);

            if ($customer_subscription->subscription->name == "Personal")
                return redirect()->route("customer.order.bin");
            return redirect()->route("customer.charity");
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors($exception->errors());
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * view the customer subscription edit page
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $customer_subscription = CustomerSubscription::where("user_id", "=", Auth::id())
                ->where("has_pre_order", "=", "1")
                ->firstOrFail();

            $subscriptions = Subscription::all();

            return view("subscription.customer_edit")
                ->with([
                    "customer_subscription" => $customer_subscription,
                    "subscriptions" => $subscriptions,
                    "pre_order_id" => $id
                ]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the subscription");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update customer subscription
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["subscription_id" => "required|integer|min:1"]);

            $request_body["subscription_id"] = $request->subscription_id;

            $user_id = Auth::id();

            $customer_subscription = CustomerSubscription::where("user_id", "=", $user_id)
                ->where("has_pre_order", "=", "1")
                ->findOrFail($id);

            $customer_subscription->update($request_body);

            PreOrder::where("user_id", "=", $user_id)
                ->where("id", "=", $request->pre_order_id)
                ->first()
                ->update($request_body);

            if ($customer_subscription->subscription->name == "Charity")
                return redirect()->route("customer.charity.edit", ["pre_order_id" => $request->pre_order_id]);
            return redirect()->route("customer.order.edit", $request->pre_order_id);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the subscription");
            return redirect()->back();
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors($exception->errors());
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
