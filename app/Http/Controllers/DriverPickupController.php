<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsNotification;
use App\Models\AdminNotification;
use App\Models\Bin;
use App\Models\DriverPickup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DriverPickupController extends Controller
{
    /**
     * List all driver pickups
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user(); $drivers = null;

            $driver_id = $user->id;

            $is_admin = false;

            $driver_pickups = DriverPickup::query();

            if ($user->hasRole("Admin")) {
                $drivers = User::role("Driver")
                    ->select("id", "first_name", "last_name")
                    ->get();

                if (isset($request->driver_id) && $request->driver_id) {

                    $driver_id = $request->driver_id;

                    $driver_pickups->where("driver_id", "=", $request->driver_id);

                }

                if (isset($request->pickup_date) && $request->pickup_date) {

                    $start = Carbon::parse($request->pickup_date)->startOfDay();
                    $end = Carbon::parse($request->pickup_date)->endOfDay();

                    $driver_pickups->whereBetween("pickup_date", [$start, $end]);

                }

                $driver_pickups = $driver_pickups->get();

                $is_admin = true;

            } else {

                $driver_pickups = $driver_pickups->where("driver_id", "=", $user->id)
                    ->get();

            }

            $i = 1;

            return view(
                "driver_pickup.index",
                compact("driver_pickups", "i", "drivers", "driver_id", "is_admin")
            );

        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create a pickup
     * @param $bin_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($bin_id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();

            $bin = Bin::query()
                ->select("id", "order_id")
                ->find($bin_id);

            if (!$bin)
                throw new ModelNotFoundException("Unable to find the bin");

            if (!$bin->order_id)
                throw new ModelNotFoundException("This bin is not allocated to any customer");

            $driver_pickup = DriverPickup::create([
                "user_id" => $bin->order->user_id,
                "user_address_id" => $bin->order->user_address_id,
                "pickup_date" => Carbon::now()->format("Y-m-d H:i:s"),
                "status" => "picked",
                "bin_id" => $bin_id,
                "driver_id" => auth()->id()
            ]);

            $admin_notification = AdminNotification::create([
                "user_id" => $bin->order->user_id,
                "description" => "Your bin was picked at {$driver_pickup->pickup_date}"
            ]);

            DB::commit();

            Session::flash("success", "Bin pickup data was created successfully");

            return redirect()->route("driver.pickup");

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
     * Delete driver pick up
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            DriverPickup::findOrFail($id)
                ->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
