<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    /** Show notifications to admin
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $user_id = null;

            $notifications = Notification::query()
                ->with(["pickup" => function($q) {
                    $q->with("userAddress");
                }, "user" => function ($q) {
                    $q->select("id", "first_name", "last_name");
                }]);

            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();

            if (isset($request->user_id) && $request->user_id) {
                $notifications->where("user_id", "=", $request->user_id);
                $user_id = $request->user_id;
            }

            if (isset($request->created_at) && $request->created_at) {
                $created_at_start = Carbon::parse($request->created_at)->startOfDay()->format("Y-m-d H:i:s");
                $created_at_end = Carbon::parse($request->created_at)->endOfDay()->format("Y-m-d H:i:s");
                $notifications->whereBetween("created_at", [$created_at_start, $created_at_end]);
            }

            $notifications = $notifications->get();

            return view("notification.admin")
                ->with(["notifications" => $notifications, "user_id" => $user_id, 'i' => 1, "users" => $users]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete notification
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            Notification::findOrFail($id)
                ->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
