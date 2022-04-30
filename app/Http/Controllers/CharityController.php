<?php

namespace App\Http\Controllers;

use App\Models\Charity;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CharityController extends Controller
{
    /**
     * Fetch all charities
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $charities = Charity::all();
            return view("charity.index")
                ->with(["charities" => $charities, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add charity form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("charity.create")
                ->with(["subscriptions" => Subscription::all()]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add a new charity
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "subscription_id" => "required|integer|min:1",
                "name" => "required|unique:charities,name",
                "account_name" => "required",
                "account_number" => "required|unique:customer_accounts,account_number",
                "bsb" => "required",
                "bank_name" => "required",
                "branch" => "required"
            ]);

            $request_body = $request->only(
                "subscription_id", "name", "account_name", "account_number", "bsb", "bank_name", "branch"
            );

            Charity::create($request_body);

            Session::flash("success", "Charity created successfully");

            return redirect()->route("charity");
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
     * View the charity edit form
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $charity = Charity::findOrFail($id);
            return view("charity.edit")
                ->with(["charity" => $charity, "subscriptions" => Subscription::all()]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the carity");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update the charity
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "subscription_id" => "required|integer|min:1",
                "name" => "required|unique:charities,name,$id",
                "account_name" => "required",
                "account_number" => "required|unique:customer_accounts,account_number,$id",
                "bsb" => "required",
                "bank_name" => "required",
                "branch" => "required"
            ]);

            $charity = Charity::findOrFail($id);

            $request_body = $request->only(
                "subscription_id", "name", "account_name", "account_number", "bsb", "bank_name", "branch"
            );

            $charity->update($request_body);

            Session::flash("success", "Charity updated successfully");

            return redirect()->route("charity");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the carity");
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
     * Delete the charity
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $charity = Charity::findOrFail($id);
            $charity->delete();
            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
