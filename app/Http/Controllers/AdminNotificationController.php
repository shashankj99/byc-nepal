<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AdminNotificationController extends Controller
{
    /**
     * Get all admin notification
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $admin_notifications = AdminNotification::withTrashed()->get();
            $i=1;
            return view("admin_notification.index", compact("admin_notifications", "i"));
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create a new admin notification
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            $customers = User::role("Customer")
                ->select("id", "first_name", "last_name")
                ->get();

            return view("admin_notification.create", compact("customers"));
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Store Notifications
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "user_id" => "required",
                "description" => "required"
            ]);

            $request_body = $request->only("user_id", "description");

            if ($request_body["user_id"] == "all") {
                $users = User::role("Customer")
                    ->select("id")
                    ->get();

                $bulk_request_body = [];

                foreach ($users as $key => $user) {
                    $bulk_request_body[$key]["user_id"] = $user->id;
                    $bulk_request_body[$key]["description"] = $request_body["description"];
                    $bulk_request_body[$key]["created_at"] = Carbon::now()->format("Y-m-d H:i:s");
                }

                AdminNotification::insert($bulk_request_body);
            } else AdminNotification::create($request_body);

            Session::flash("success", "Notification was sent successfully");
            return redirect()->route("admin.notification");
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
     * Delete Notification
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $admin_notification = AdminNotification::find($id);

            if (!$admin_notification) {
                AdminNotification::withTrashed()
                    ->findOrFail($id)
                    ->forceDelete();
            } else $admin_notification->forceDelete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
