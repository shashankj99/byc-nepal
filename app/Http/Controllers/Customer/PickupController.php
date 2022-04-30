<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerBin;
use App\Models\Pickup;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PickupController extends Controller
{
    /**
     * View customer pickup page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user_id = auth()->id();

            $locations = UserAddress::with("customerBins")
                ->where("user_id", "=", $user_id)
                ->get();

            return view("pickup.customer.index")
                ->with(["locations" => $locations]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add a pickup request
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_address_id" => "required|integer|min:1",
                "no_of_bins-*" => "required|integer|min:1"
            ]);

            $request_param = $request->all();

            if (isset($request_param["no_of_bins-".$request_param["user_address_id"]]))
                $no_of_bins = $request_param["no_of_bins-".$request_param["user_address_id"]];
            else
                throw new \Exception("Something went wrong");

            if ($no_of_bins < 1)
                throw ValidationException::withMessages([
                    "no_of_bins" => "Your order must not be less than 1"
                ]);

            $user_id = auth()->id();

            $total_customer_bins = CustomerBin::where("user_id", "=", $user_id)
                ->where("user_address_id", "=", $request->user_address_id)
                ->count("id");

            if ($no_of_bins > $total_customer_bins)
                throw ValidationException::withMessages([
                    "no_of_bins" => "Your order should not be greater than $total_customer_bins"
                ]);

            $request_body["user_address_id"] = $request_param["user_address_id"];
            $request_body["no_of_bins"] = $no_of_bins;
            $request_body["user_id"] = $user_id;
            $request_body["status"] = "pending";

            Pickup::create($request_body);

            Session::flash("success", "Your pickup request has been submitted to admin");
            return redirect()->route("dashboard");

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
     * Show all pickup orders
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPickups()
    {
        try {
            $pickups = Pickup::with(["userAddress" => function ($q) {
                $q->select("id", "address");
            }])
                ->where("user_id", "=", auth()->id())
                ->select("id", "user_address_id", "no_of_bins", "pickup_date", "status")
                ->get();

            return view("pickup.customer.view")
                ->with(["pickups" => $pickups, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show Pickup form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPickupForm()
    {
        try {
            $pickups = Pickup::with(["userAddress" => function($q) {
                $q->select("id", "address");
            }])
                ->select("id", "user_address_id", "no_of_bins", "pickup_date")
                ->where("user_id", "=", auth()->id())
                ->where("status", "=", "accepted")
                ->get();

            return view("pickup.customer.select_pickup")
                ->with(["pickups" => $pickups]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Check pickup and redirect to calendar page
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkPickup(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "pickup_id" => "required|integer|min:1",
                "no_of_bins" => "required|integer|min:1"
            ]);

            $pickup = Pickup::where("user_id", "=", auth()->id())
                ->where("status", "=", "accepted")
                ->findOrFail($request->pickup_id);

            if ($request->no_of_bins > $pickup->no_of_bins)
                throw ValidationException::withMessages([
                    'no_of_bins' => "No of bins should not be greater than {$pickup->no_of_bins}"
                ]);

            return redirect()->route("customer.pickup.date.show", $pickup->id);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", $modelNotFoundException->getMessage());
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
     * Show pickup calendar
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function pickupDate($id)
    {
        try {
            $pickup = Pickup::where("user_id", "=", auth()->id())
                ->where("status", "=", "accepted")
                ->findOrFail($id);

            $exploded_pickup_date = explode(" ", $pickup->pickup_date);

            $old_date = $exploded_pickup_date[0];
            $old_time = $exploded_pickup_date[1];

            return view("pickup.customer.calendar")
                ->with([
                    "pickup" => $pickup, "old_date" => $old_date, "old_time" => $old_time
                ]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", $modelNotFoundException->getMessage());
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Change pickup date
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePickupDate($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "old_date" => "required|date",
                "old_time" => "required",
                "new_date" => "required|date",
                "new_time" => "required"
            ]);

            $pickup = Pickup::where("user_id", "=", auth()->id())
                ->where("status", "=", "accepted")
                ->findOrFail($id);

            $pickup_date = "{$request->new_date} {$request->new_time}";
            $parsed_date = Carbon::parse($pickup_date)->format("Y-m-d H:i:s");

            $pickup->update(["pickup_date" => $parsed_date, "status" => "pending"]);

            Session::flash("success", "New pickup date assigned successfully. It's awaiting admin approval");
            return redirect()->route("dashboard");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", $modelNotFoundException->getMessage());
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
}
