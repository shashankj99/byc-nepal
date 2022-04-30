<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Charity;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Show all bin orders
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $user_id = null;

            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();

            $orders = Order::query()
                ->with(["user" => function ($q) {
                    $q->select("id", "first_name", "last_name");
                }]);

            if (isset($request->user_id) && $request->user_id) {
                $orders->where("user_id", "=", $request->user_id);
                $user_id = $request->user_id;
            }

            if (isset($request->created_at) && $request->created_at) {
                $created_at = Carbon::parse($request->created_at)->startOfDay()->format("Y-m-d H:i:s");
                $orders->where("created_at", ">=", $created_at);
            }

            $orders = $orders->get();

            return view("order.index")
                ->with(
                    [
                        "users" => $users,
                        "orders" => $orders,
                        "i" => 1,
                        "user_id" => $user_id
                    ]
                );
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View Create Bin Order Form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            $charities = Charity::all();
            $subscriptions = Subscription::all();
            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();
            return view("order.create")
                ->with(["charities" => $charities, "subscriptions" => $subscriptions, "users" => $users]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create a new Bin Order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_id" => "required|integer|min:1",
                "user_address_id" => "required|integer|min:1",
                "subscription_id" => "required|integer|min:1",
                "amount" => "required|min:0|max:40",
                "order_status" => "required|in:accepted,pending,rejected",
                "payment_status" => "required|in:incomplete,complete",
                "bin_type" => "required|in:drum-bin,wheelie-bin",
            ]);

            $request_body = $request->only(
                "user_id", "user_address_id", "subscription_id", "amount", "order_status", "payment_status",
                "bin_type", "card_type", "charity", "payment_type"
            );

            Order::create($request_body);

            AdminNotification::create([
                "user_id" => $request_body["user_id"],
                "description" => "Your bin order was created successfully by admin"
            ]);

            Session::flash("success", "Bin Order Created Successfully");
            return redirect()->route("orders");
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
     * Function to view order details
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $order = Order::findOrFail($id);
            $charities = Charity::all();
            $subscriptions = Subscription::all();
            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();
            return view("order.edit")
                ->with(["charities" => $charities, "subscriptions" => $subscriptions, "users" => $users, "order" => $order]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the order");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update the bin order
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_id" => "required|integer|min:1",
                "user_address_id" => "required|integer|min:1",
                "subscription_id" => "required|integer|min:1",
                "amount" => "required|min:0|max:40",
                "order_status" => "required|in:accepted,pending,rejected",
                "payment_status" => "required|in:incomplete,complete",
                "bin_type" => "required|in:drum-bin,wheelie-bin",
            ]);

            $request_body = $request->only(
                "user_id", "user_address_id", "subscription_id", "amount", "order_status", "payment_status",
                "bin_type", "card_type", "charity", "payment_type"
            );

            Order::findOrFail($id)
                ->update($request_body);

            AdminNotification::create([
                "user_id" => $request_body["user_id"],
                "description" => "Your bin order was successfully updated by admin"
            ]);

            Session::flash("success", "Bin Order Updated Successfully");
            return redirect()->route("orders");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the order");
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
     * Delete the order
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            AdminNotification::create([
                "user_id" => $order->user_id,
                "description" => "Your bin order was deleted by admin"
            ]);

            $order->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Get user unaccepted orders
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrdersByUserId($user_id): \Illuminate\Http\JsonResponse
    {
        try {
            $orders = Order::query()
                ->select("id", "bin_type")
                ->where("user_id", "=", $user_id)
                ->where("order_status", "=", "pending")
                ->get();

            return response()->json(["data" => $orders]);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 500);
        }
    }
}
