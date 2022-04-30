<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class DriverController extends Controller
{
    /**
     * View all drivers
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $drivers = User::query()
                ->role("Driver");

            if (isset($request->status) && $request->status)
                $drivers->where("status", "=", $request->status);

            $drivers = $drivers->get();

            return view("driver.index")
                ->with(["users" => $drivers, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show create driver form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("driver.create");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add a new driver
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // validate the request
            $request->validate([
                "first_name" => "required",
                "last_name" => "required",
                "mobile_number" => "required|integer|unique:users,mobile_number",
                "email" => "required|email|unique:users,email",
                "depot" => "required",
                "license" => "required|unique:drivers,license",
                "device_number" => "required|unique:drivers,device_number",
                "status" => "required|in:active,inactive",
            ]);

            // get the required keys for user
            $user_request_body = $request->only("first_name", "last_name", "email", "mobile_number", "status");
            // make admin created flag true
            $user_request_body["is_admin_created"] = "1";
            // create a default password
            $user_request_body["password"] = Hash::make("Byc@1234");

            // get the required keys for driver
            $driver_request_body = $request->only("depot", "license", "device_number", "route");

            // begin transaction
            DB::beginTransaction();

            // find or create the role Driver
            $role = Role::firstOrCreate(["name" => "Driver"]);

            // create user
            $user = User::create($user_request_body);

            // assign the role driver to the user
            $user->assignRole($role->name);

            // create driver from user
            $user->driver()->create($driver_request_body);

            // commit the transaction
            DB::commit();

            // return to index page
            Session::flash("success", "Driver added successfully");
            return redirect()->route("driver");
        } catch (ValidationException $validationException) {
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
     * View driver edit page
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $driver = User::with("driver")->findOrFail($id);
            return view("driver.edit")
                ->with(["user" => $driver]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the driver");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update Driver
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // find user and it's associated driver data
            $user = User::with("driver")->findOrFail($id);

            $request->validate([
                "first_name" => "required",
                "last_name" => "required",
                "mobile_number" => "required|integer|unique:users,mobile_number,$id",
                "email" => "required|email|unique:users,email,$id",
                "depot" => "required",
                "license" => "required|unique:drivers,license,{$user->driver->id}",
                "device_number" => "required|unique:drivers,device_number,{$user->driver->id}",
                "status" => "required|in:active,inactive",
            ]);

            // get the required keys for user
            $user_request_body = $request->only("first_name", "last_name", "email", "mobile_number", "status");

            // get the required keys for driver
            $driver_request_body = $request->only("depot", "license", "device_number", "route");

            // check if password exists in the request
            if (isset($request->password) && $request->password) {
                $request->validate(["password" => "required|min:8|confirmed"]);
                $user_request_body["password"] = Hash::make($request->password);
            }

            // check if off_board_at date exists in the request
            if (isset($request->off_board_at) && $request->off_board_at)
                $user_request_body["off_board_at"] = Carbon::parse($request->off_board_at)->startOfDay()
                    ->format("Y-m-d H:i:s");

            // update user data
            $user->update($user_request_body);

            // update driver data
            $user->driver->update($driver_request_body);

            Session::flash("success", "Driver Updated Successfully");
            return redirect()->route("driver");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the driver");
            return redirect()->back();
        } catch (ValidationException $validationException) {
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
     * Delete the driver
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            Driver::findOrfail($id)
                ->delete();
            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
