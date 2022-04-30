<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Check if user has default account
     * @param $user_id
     * @return mixed
     */
    private function checkUserHasDefaultAccount($user_id)
    {
        return CustomerAccount::where("user_id", "=", $user_id)
            ->where("is_default", "=", "1")
            ->select("id")
            ->first();
    }

    /**
     * Get all customer accounts
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $customer_accounts = CustomerAccount::query();

            $user_id = auth()->id();

            // initialize an empty array
            $users = [];

            if (auth()->user()->hasRole("Admin")) {
                $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                    ->where('status', "=", "active")
                    ->get()
                    ->toArray();

                if (isset($request->user_id) && $request->user_id) {
                    $user_id = $request->user_id;
                    $customer_accounts->where("user_id", "=", $user_id);
                }
            } else {
                $customer_accounts->where("user_id", "=", $user_id);
            }

            $customer_accounts = $customer_accounts->get();

            return view("account.index")
                ->with(["customer_accounts" => $customer_accounts, 'i' => 1, "users" => $users, "user_id" => $user_id]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * View create account page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            if (auth()->user()->hasRole("Admin"))
                $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                    ->where('status', "=", "active")
                    ->get()
                    ->toArray();
            else
                $users = [];
            return view("account.create")
                ->with(["users" => $users]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * store customer account
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "account_name" => "required",
                "account_number" => "required|unique:customer_accounts,account_number",
                "bsb" => "required",
                "bank_name" => "required",
                "branch" => "required"
            ]);

            $request_body = $request->only("account_name", "account_number", "bsb", "bank_name", "branch");

            $user = auth()->user();

            $user_id = $user->id;

            if (auth()->user()->hasRole("Admin")) {
                $request->validate(["user_id" => "required|integer|min:1"]);
                $user_id = $request->user_id;
            }

            $check_if_user_has_default_account = $this->checkUserHasDefaultAccount($user_id);
            $is_default = $check_if_user_has_default_account ? "0" : "1";

            $request_body["user_id"] = $user_id;
            $request_body["is_default"] = $is_default;

            CustomerAccount::create($request_body);

            Session::flash("success", "Account Added Successfully");
            return redirect()->route("customer.account");
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
     * View edit customer account page
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            if (auth()->user()->hasRole("Admin"))
                $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                    ->where('status', "=", "active")
                    ->get()
                    ->toArray();
            else
                $users = [];

            $customer_account = CustomerAccount::query();

            if (auth()->user()->hasRole("Customer"))
                $customer_account->where("user_id", "=", auth()->id());

            $customer_account = $customer_account->findOrFail($id);

            return view("account.edit")
                ->with(["customer_account" => $customer_account, "users" => $users]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the customer account");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update customer account
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "account_name" => "required",
                "account_number" => "required|unique:customer_accounts,account_number,$id",
                "bsb" => "required",
                "bank_name" => "required",
                "id_default" => "sometimes|in:0,1",
                "branch" => "required"
            ]);

            $user_id = Auth::id();

            $customer_account = CustomerAccount::query();

            if (auth()->user()->hasRole("Admin")) {
                $request->validate(["user_id" => "required|integer|min:1"]);
                $user_id = $request->user_id;
            } else $customer_account->where("user_id", "=", $user_id);

            $customer_account = $customer_account->findOrFail($id);

            $request_body = $request->only("account_name", "account_number", "bsb", "bank_name", "is_default", "branch");
            $request_body["user_id"] = $user_id;

            $customer_account->update($request_body);

            Session::flash("success", "Account Updated Successfully");

            return redirect()->route("customer.account");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the customer account");
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
     * Delete customer account
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $customer_account = CustomerAccount::query();

            if (auth()->user()->hasRole("Customer"))
                $customer_account->where("user_id", "=", auth()->id());

            $customer_account = $customer_account->findOrFail($id);

            $customer_account->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Make account default
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeAccountDefault($id): \Illuminate\Http\JsonResponse
    {
        try {
            $customer_account = CustomerAccount::query();

            if (auth()->user()->hasRole("Customer"))
                $customer_account->where("user_id", "=", auth()->id());

            $customer_account = $customer_account->findOrFail($id);

            $customer_account->update(["is_default" => "1"]);

            CustomerAccount::where("id", "!=", $id)
                ->where("user_id", "=", $customer_account->user_id)
                ->update(["is_default" => "0"]);

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
