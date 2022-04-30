<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * View all subscriptions
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $subscriptions  = Subscription::all();
            return view("subscription.index")
                ->with(["subscriptions" => $subscriptions, "i" => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show Subscription create form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("subscription.create");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Store subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "name" => "required|unique:subscriptions,name",
                "description" => "required"
            ]);

            $request_body = $request->only("name", "description");

            Subscription::create($request_body);

            Session::flash("success", "Subscription Created Successfully");

            return redirect()->route("subscription");
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
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);

            return view("subscription.edit")
                ->with("subscription", $subscription);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", $modelNotFoundException->getMessage());
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update subscription
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "name" => "required|unique:subscriptions,name,".$id,
                "description" => "required"
            ]);

            $request_body = $request->only("name", "description");

            $subscription = Subscription::findOrFail($id);

            $subscription->update($request_body);

            Session::flash("success", "Subscription Updated Successfully");

            return redirect()->route("subscription");
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", $modelNotFoundException->getMessage());
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Method to delete the subscription
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();
            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
