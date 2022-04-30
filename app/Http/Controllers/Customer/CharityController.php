<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\User;
use App\Models\UserCharity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CharityController extends Controller
{
    /**
     * get all charities
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user = User::with(["customerSubscriptions" => function ($q) {
                $q->with(["subscription" => function($q) {
                    $q->where("name", "=", "Charity");
                }]);
            }])
                ->select("id")
                ->where("id", "=", Auth::id())
                ->first();

            $has_charity = false;

            foreach ($user->customerSubscriptions as $customerSubscription) {
                if (!$customerSubscription->subscription) continue;
                if ($customerSubscription->subscription->name == "Charity") $has_charity = true;
            }

            if (!$has_charity)
                return redirect()->route("customer.order.bin");

            $charities = Charity::all();
            return view("charity.customer")
                ->with(["charities" => $charities]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Assign charity to the user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["charity_id" => "required|integer|min:1"]);

            $request_body["charity_id"] = $request->charity_id;
            $request_body["user_id"] = Auth::id();

            UserCharity::create($request_body);

            return redirect()->route("customer.order.bin");
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
     * show user charity edit page
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request)
    {
        try {
            $user_charity = UserCharity::where("user_id", "=", Auth::id())
                ->where("has_pre_order", "=", "1")
                ->first();

            $charities = Charity::all();

            return view("charity.customer_edit")
                ->with([
                    "user_charity" => $user_charity,
                    "charities" => $charities,
                    "pre_order_id" => $request->pre_order_id
                ]);
        } catch (ModelNotFoundException $exception) {
            Session::flash("error", "Unable to find the charity");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update user charity
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["charity_id" => "required|integer|min:1"]);

            $request_body["charity_id"] = $request->charity_id;

            $user_id = Auth::id();

            $user_charity = UserCharity::where("user_id", "=", $user_id)
                ->where("has_pre_order", "=", "1")
                ->first();

            $request_body["user_id"] = $user_id;

            if ($user_charity)
                $user_charity->update($request_body);
            else UserCharity::create($request_body + ["has_pre_order" => "1"]);

            return redirect()->route("customer.order.edit", $request->pre_order_id);
        } catch (ModelNotFoundException $exception) {
            Session::flash("error", "Unable to find the charity");
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
