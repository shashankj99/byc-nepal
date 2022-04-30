<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\CustomerSubscription;
use App\Models\Order;
use App\Models\PreOrder;
use App\Models\UserCharity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * View the payment page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentTypePage(Request $request)
    {
        try {
            return view("payment.customer.type")
                ->with(["pre_order_id" => $request->pre_order_id]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Method to add payment type to pre order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["payment_type" => "required|in:full,installment"]);

            $pre_order = PreOrder::where("user_id", "=", Auth::id())
                ->where("bin_type", "=", "wheelie-bin")
                ->whereNull("payment_type")
                ->where("status", "=", "incomplete")
                ->findOrFail($request->pre_order_id);

            $request_body = $request->only("payment_type") + ["total_amount" => 40.0];

            $pre_order->update($request_body);

            return redirect()->route("customer.order.confirm");
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show payment type form
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $pre_order = PreOrder::where("user_id", "=", Auth::id())
                ->where("bin_type", "=", "wheelie-bin")
                ->where("status", "=", "incomplete")
                ->whereNotNull("payment_type")
                ->select("id", "payment_type")
                ->findOrFail($id);

            return view("payment.customer.type_edit")
                ->with(["pre_order" => $pre_order]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the payment type");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update payment type
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["payment_type" => "required|in:full,installment"]);

            $pre_order = PreOrder::where("user_id", "=", Auth::id())
                ->where("bin_type", "=", "wheelie-bin")
                ->where("status", "=", "incomplete")
                ->findOrFail($id);

            $pre_order->update(["payment_type" => $request->payment_type]);

            return redirect()->route("customer.order.confirm");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the payment type");
            return redirect()->back();
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View checkout form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function viewCheckoutPage()
    {
        try {
            DB::beginTransaction();

            $user_id = Auth::id();

            $pre_order = PreOrder::with(["subscription", "user" => function ($q) {
                $q->with(["userAddresses" => function ($query) {
                    $query->select("id", "user_id")
                        ->where("is_default", "=", "1");
                }]);
            }])
                ->where("user_id", "=", $user_id)
                ->where("status", "=", "incomplete")
                ->latest("id")
                ->firstOrFail();

            if (!isset($pre_order->user->userAddresses[0]))
                throw new ModelNotFoundException("User doesn't have any default address");

            if ($pre_order->payment_type == "full" && $pre_order->bin_type == "wheelie-bin")
                return view("payment.customer.create");

            $charity_name = $this->getCharityName($pre_order, $user_id);

            Order::create([
                'user_id' => $user_id,
                'subscription_id' => $pre_order->subscription_id,
                'charity' => $charity_name,
                'card_type' => null,
                'amount' => ($pre_order->bin_type == "wheelie-bin") ? 40.00 : 0,
                'order_status' => "pending",
                'payment_status' => "complete",
                'bin_type' => $pre_order->bin_type,
                'payment_type' => "full",
                'user_address_id' => $pre_order->user->userAddresses[0]->id
            ]);

            CustomerSubscription::where("user_id", "=", $user_id)
                ->where("subscription_id", "=", $pre_order->subscription_id)
                ->where("has_pre_order", "=", "1")
                ->firstOrFail()
                ->delete();

            $pre_order->delete();

            DB::commit();

            Session::flash("success", "Order placed successfully");
            return redirect()->route("dashboard");
        } catch (ModelNotFoundException $modelNotFoundException) {
            DB::rollBack();
            Session::flash("error", $modelNotFoundException->getMessage());
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    public function storeCheckoutToken(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "card_name" => "required",
                "card_number" => "required|integer|min:1",
                "cvc" => "required|integer|min:1",
                "exp_month" => "required|integer|min:1,12",
                "exp_year" => "required|date_format:Y"
            ]);

            DB::beginTransaction();

            $user_id = Auth::id();

            $pre_order = PreOrder::with("subscription")
                ->with(["user" => function ($q) {
                    $q->with(["userAddresses" => function ($query) {
                        $query->select("id", "user_id")
                            ->where("is_default", "=", "1");
                    }]);
                }])
                ->where("user_id", "=", $user_id)
                ->where("status", "=", "incomplete")
                ->latest("id")
                ->firstOrFail();

            if (!isset($pre_order->user->userAddresses[0]))
                throw new ModelNotFoundException("User doesn't have any default address");

            if ($pre_order->payment_type == "full") {
                $stripe = new \Stripe\StripeClient(
                    env("STRIPE_SECRET")
                );

                $stripe_token = $stripe->tokens->create([
                    'card' => [
                        'number' => $request->card_number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'cvc' => $request->cvc,
                    ],
                ]);

                $stripe_charge = $stripe->charges->create([
                    "amount" => "4000",
                    "currency" => "aud",
                    "source" => $stripe_token->id,
                    "description" => "Payment for BYC Wheelie-Bin"
                ]);
            }

            $charity_name = $this->getCharityName($pre_order, $user_id);

            Order::create([
                'user_id' => $user_id,
                'subscription_id' => $pre_order->subscription_id,
                'charity' => $charity_name,
                'card_type' => $stripe_charge->source->brand,
                'amount' => 40.00,
                'order_status' => "pending",
                'payment_status' => "complete",
                'bin_type' => $pre_order->bin_type,
                'payment_type' => $pre_order->payment_type,
                'user_address_id' => $pre_order->user->userAddresses[0]->id
            ]);

            CustomerSubscription::where("user_id", "=", $user_id)
                ->where("subscription_id", "=", $pre_order->subscription_id)
                ->where("has_pre_order", "=", "1")
                ->firstOrFail()
                ->delete();

            $pre_order->delete();

            DB::commit();

            Session::flash("success", "Order placed successfully");
            return redirect()->route("dashboard");
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (ModelNotFoundException $modelNotFoundException) {
            DB::rollBack();
            Session::flash("error", $modelNotFoundException->getMessage());
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @param $pre_order
     * @param $user_id
     * @return null
     */
    public function getCharityName($pre_order, $user_id)
    {
        if ($pre_order->subscription->name == "Charity") {
            $user_charity = UserCharity::where("user_id", "=", $user_id)
                ->where("has_pre_order", "=", "1")
                ->first();

            if ($user_charity) {
                $charity = Charity::where("id", "=", $user_charity->charity_id)
                    ->select("name")
                    ->first();

                $user_charity->delete();

                if ($charity)
                    return $charity->name;
            }
        }

        return null;
    }
}
