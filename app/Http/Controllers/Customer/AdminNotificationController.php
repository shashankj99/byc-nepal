<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminNotificationController extends Controller
{
    /**
     * View Notifications given by admin
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $notifications = AdminNotification::query()
                ->where("user_id", "=", auth()->id());

            $updateRecords = $notifications->update(["is_seen" => "1"]);

            $notifications = $notifications->whereNull("deleted_at")
                ->get();
            return view("admin_notification.customer", compact("notifications"));
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
    public function deleteNotification($id): \Illuminate\Http\JsonResponse
    {
        try {
            AdminNotification::findOrFail($id)->delete();
            return response()->json(["message" => "successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
