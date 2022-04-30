<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Pickup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $pickups = Pickup::with(["userAddress" => function($q) {
                $q->select("id", "address");
            }])
                ->where("user_id", "=", auth()->id())
                ->where("status", "=", "accepted")
                ->get();

            return view("notification.customer")
                ->with(["pickups" => $pickups]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Store Notification in DB
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "pickup_id" => "required|integer|min:1",
                "description" => "required"
            ]);

            $user_id = auth()->id();

            $request_body = $request->only("pickup_id", "description");

            $request_body["user_id"] = $user_id;

            Notification::create($request_body);

            Session::flash("success", "Your notification was sent to Backyard Cash");
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
     * List all notifications for customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listAllNotifications()
    {
        try {
            $notifications = Notification::with(["pickup" => function($q) {
                    $q->with("userAddress");
                }])
                ->where("user_id", "=", auth()->id())
                ->get();

            return view("notification.index")
                ->with(["notifications" => $notifications, 'i' => 1]);
        } catch (\Exception $exception){
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
