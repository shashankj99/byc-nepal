<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsNotification;
use App\Models\AdminNotification;
use App\Models\Pickup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PickupController extends Controller
{
    /**
     * View All pickup orders
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

            $pickups = Pickup::query()
                ->with(["userAddress", "user" => function($q) {
                    $q->select("id", "first_name", "last_name");
                }]);

            if (isset($request->user_id) && $request->user_id) {
                $pickups->where("user_id", "=", $request->user_id);
                $user_id = $request->user_id;
            }

            if (isset($request->created_at) && $request->created_at) {
                $created_at = Carbon::parse($request->created_at)->startOfDay()->format("Y-m-d H:i:s");
                $created_at_end = Carbon::parse($request->created_at)->endOfDay()->format("Y-m-d H:i:s");
                $pickups->whereBetween("created_at", [$created_at, $created_at_end]);
            }

            $pickups = $pickups->get();

            return view("pickup.index")
                ->with(
                    [
                        "users" => $users,
                        "pickups" => $pickups,
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
     * Show Form with pickup date and time
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function showPickUpForm($id)
    {
        try {
            $pickup = Pickup::where("status", "=", "pending")->findOrFail($id);
            return view("pickup.assign")
                ->with(["pickup" => $pickup]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * add pickup date and change pickup status
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "pickup_date" => "required|date",
            ]);

            $parsed_date = Carbon::parse($request->pickup_date)->format("Y-m-d");

            $pickup = Pickup::findOrFail($id);

            $pickup->update(["pickup_date" => $parsed_date, "status" => "accepted"]);

            $admin_notification = AdminNotification::create([
                "user_id" => $pickup->user_id,
                "description" => "Your pickup order has been approved and is dated at {$pickup->pickup_date_formatted}"
            ]);

            dispatch(new SendSmsNotification($pickup->user->mobile_number, $admin_notification->description));

            Session::flash("success", "Pickup date added successfully");
            return redirect()->route("pickup");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the pickup");
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
     * Update status to accepted
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptStatus($id): \Illuminate\Http\RedirectResponse
    {
        try {
            Pickup::findOrFail($id)
                ->update(["status" => "accepted"]);

            Session::flash("success", "Pickup order was accepted");
            return redirect()->route("pickup");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the pickup");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create new pickup order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();
            return view("pickup.create")
                ->with(["users" => $users]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create a new pickup request
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_id" => "required|integer|min:1",
                "user_address_id" => "required|integer|min:1",
                "no_of_bins" => "required|integer|min:1",
                "pickup_date" => "required|date",
                "pickup_time" => "required",
                "status" => "required|in:pending,accepted,rejected"
            ]);

            $request_body = $request->only("user_id", "user_address_id", "no_of_bins", "status");

            $request_body["pickup_date"] = Carbon::parse($request->pickup_date)->format("Y-m-d");

            Pickup::create($request_body);

            AdminNotification::create([
                "user_id" => $request_body["user_id"],
                "description" => "Your pickup order was created successfully by admin"
            ]);

            Session::flash("success", "Pickup order placed successfully");
            return redirect()->route("pickup");
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
     * View the Pickup Edit form
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();

            $pickup = Pickup::findOrFail($id);
            return view("pickup.edit")
                ->with(["users" => $users, "pickup" => $pickup]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the pickup");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update Pickup Order
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePickup($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_id" => "required|integer|min:1",
                "user_address_id" => "required|integer|min:1",
                "no_of_bins" => "required|integer|min:1",
                "pickup_date" => "required|date",
                "status" => "required|in:pending,accepted,rejected"
            ]);

            DB::beginTransaction();

            $request_body = $request->only("user_id", "user_address_id", "no_of_bins", "status");

            $request_body["pickup_date"] = Carbon::parse($request->pickup_date)->format("Y-m-d");

            $pickup = Pickup::findOrFail($id);

            $old_pickup_date = $pickup->pickup_date;

            $pickup->update($request_body);

            if ($old_pickup_date != $pickup->pickup_date) {
                $admin_notification = AdminNotification::create([
                    "user_id" => $request_body["user_id"],
                    "description" => "Your pickup order has been approved and is dated at {$pickup->pickup_date_formatted}"
                ]);

                dispatch(new SendSmsNotification($pickup->user->mobile_number, $admin_notification->description));
            }

            AdminNotification::create([
                "user_id" => $request_body["user_id"],
                "description" => "Your pickup order was successfully updated by admin"
            ]);

            DB::commit();

            Session::flash("success", "Pickup order updated successfully");
            return redirect()->route("pickup");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the pickup");
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
     * Delete Pickup Order
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $pickup = Pickup::findOrFail($id);

            AdminNotification::create([
                "user_id" => $pickup->user_id,
                "description" => "Your pickup order was deleted by admin"
            ]);

            $pickup->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
