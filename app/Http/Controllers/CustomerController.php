<?php

namespace App\Http\Controllers;

use App\Jobs\SendDefaultPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $customers = User::query()->role('Customer')
                ->select("id", "first_name", "last_name", "off_board_at", "created_at", "myob_uid")
                ->with(["userAddresses" => function ($query) {
                    $query
                        ->select("id", "user_id", "address", "type", "suburban", "postal_code")
                        ->where("is_default", "=", "1");
                }]);

            if (isset($request->status) && $request->status)
                $customers->where("status", "=", $request->status);

            $customers = $customers->get()->toArray();
            return view("customer.index")
                ->with(['customers' => $customers, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View create user form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("customer.create");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add a new customer
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "first_name" => "required",
                "last_name" => "required",
                "mobile_number" => "required|integer|unique:users,mobile_number",
                "email" => "required|email|unique:users,email",
            ]);

            $request_body = $request->only("first_name", "last_name", "mobile_number", "email");

            $request_body["password"] = Hash::make("Byc@1234");
            $request_body["status"] = "active";
            $request_body["is_admin_created"] = "1";

            DB::beginTransaction();

            $user = User::create($request_body);

            // get customer role or create role
            $role = Role::firstOrCreate(["name" => "Customer"]);

            // assign role to the user
            $user->assignRole($role["name"]);

            DB::commit();

            dispatch(new SendDefaultPasswordMail($user->email));

            Session::flash("success", "Customer added successfully");
            return redirect()->route("customer");
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
     * Show customer details
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $customer = User::with(["bins" => function ($q) {
                $q->with(["order" => function($q) {
                    $q->with("subscription");
                }]);
            }, "myobTransactions", "customerAccounts" => function ($q) {
                $q->select("id", "user_id", "account_number");
            }])
                ->findOrFail($id);

            return view("customer.show")
                ->with(["customer" => $customer]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the customer");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get user details in admin page
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|void
     */
    public function edit($id)
    {
        try {
            $customer = User::findOrfail($id);

            return view("customer.edit")
                ->with(["customer" => $customer]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the customer");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update the customer details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "first_name" => "required",
                "last_name" => "required",
                "email" => "required|email|unique:users,email,$id",
                "mobile_number" => "required|integer|unique:users,mobile_number,$id",
                "off_board_at" => "sometimes",
                "myob_uid" => "sometimes"
            ]);

            $request_body = $request->only(
                "first_name", "last_name", "email", "mobile_number", "myob_uid"
            );

            $is_admin = auth()->user()->hasRole("Admin");

            if ($is_admin) {
                $request->validate(["status" => "required|in:active,inactive"]);
                $request_body["status"] = $request->status;
            }

            if ($request->password) {
                $request->validate(["password" => "min:8|confirmed"]);
                $request_body["password"] = Hash::make($request->password);
            }

            if ($request->off_board_at)
                $request_body["off_board_at"] = Carbon::parse($request->off_board_at)->format("Y-m-d H:i:s");

            $user = User::findOrFail($id);
            $user->update($request_body);

            Session::flash("success", "Customer Profile Updated Successfully");
            if (auth()->user()->hasRole("Admin"))
                return redirect()->route("customer");
            return redirect()->route("dashboard");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the customer");
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
     * Delete the customer
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            User::findOrFail($id)
                ->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Method to off-board a customer
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function offBoard($id): \Illuminate\Http\JsonResponse
    {
        try {
            $customer = User::findOrFail($id);

            $customer->off_board_at = Carbon::now()->format("Y-m-d H:i:s");
            $customer->save();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
