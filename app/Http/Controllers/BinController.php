<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsNotification;
use App\Models\AdminNotification;
use App\Models\Bin;
use App\Models\CustomerBin;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class BinController extends Controller
{
    /**
     * Get all bins
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $bins = Bin::all();
            $url=config("app.url")."/bin/info/";
            return view("bin.index")
                ->with(["bins" => $bins, 'i' => 1, 'url' => $url]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function printView()
    {
        try {
            $bins = Bin::simplePaginate(16);
            $url=config("app.url")."/bin/info/";
            return view("bin.print")
                ->with(["bins" => $bins, 'url' => $url]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show Create bin form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("bin.create");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Store Bin data
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "bin_number" => "required|unique:bins,bin_number",
                "qr_code" => "required|unique:bins,qr_code",
                "bin_type" => "required|in:wheelie-bin,drum-bin",
                "status" => "required|in:allocated,unallocated",
            ]);

            $request_body = $request->only("bin_number", "qr_code", "bin_type", "status");

            Bin::create($request_body);

            Session::flash("success", "Bin added successfully");
            return redirect()->route("bin");
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
     * Show bin edit form
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $bin = Bin::findOrFail($id);
            return view("bin.edit")
                ->with(["bin" => $bin]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the bin");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "bin_number" => "required|unique:bins,bin_number,$id",
                "qr_code" => "required|unique:bins,qr_code,$id",
                "bin_type" => "required|in:wheelie-bin,drum-bin",
                "status" => "required|in:allocated,unallocated",
            ]);

            $bin = Bin::findOrFail($id);

            $request_body = $request->only("bin_number", "qr_code", "bin_type", "status");

            $bin->update($request_body);

            Session::flash("success", "Bin updated successfully");
            return redirect()->route("bin");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the bin");
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
     * Delete the bin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $bin = Bin::findOrFail($id);
            $bin->delete();
            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Show assign bin form
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function showAssignBinForm($id)
    {
        try {
            $bins = Bin::select("id", "bin_number", "bin_type")
                ->whereNull("order_id")
                ->whereNull("decomposition_date")
                ->where("status", "=", "unallocated")
                ->get();

            return view("bin.assign")
                ->with(["bins" => $bins, "order_id" => $id]);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Method to assign bin to order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function assignBinToOrder(Request $request)
    {
        try {
            // validate request
            $request->validate([
                "order_id" => "required|integer|min:1|distinct",
                "bin_id" => "required|integer|min:1"
            ]);

            // start the transaction
            DB::beginTransaction();

            // get selected order data
            $order = Order::select("id", "order_status", "user_id", "user_address_id")
                ->findOrFail($request->order_id);

            // get selected bin data
            $bin = Bin::select("id", "order_id", "status", "bin_number")
                ->findOrFail($request->bin_id);

            // update bin data
            $bin->order_id = $request->order_id;
            $bin->status = "allocated";
            $bin->save();

            // update order data
            $order->order_status = "accepted";
            $order->save();

            // create customer bin info
            CustomerBin::create([
                "user_id" => $order->user_id,
                "bin_id" => $bin->id,
                "user_address_id" => $order->user_address_id
            ]);

            // create admin notification
            $admin_notification = AdminNotification::create([
                "user_id" => $order->user_id,
                "description" => "Your bin order is approved. The bin #{$bin->bin_number} has been assigned to you",
            ]);

            // commit the transaction
            DB::commit();

            // send sms notification
            dispatch(
                new SendSmsNotification(
                    $order->user->mobile_number,
                    $admin_notification->description
                )
            );

            Session::flash("success", "Bin allocated successfully");
            return redirect()->back();
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Assign bin to user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignBinToUser(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {

            $request->validate([
                "user_id" => "required|integer|min:1",
                "user_address_id" => "required|integer|min:1",
                "order_id" => "required|integer|min:1",
                "bin_id" => "required|integer|min:1"
            ]);


            DB::beginTransaction();

            $order = Order::query()
                ->select("id", "user_id", "order_status")
                ->where("user_id", "=", $request->user_id)
                ->where("user_address_id", "=", $request->user_address_id)
                ->where("id", "=", $request->order_id)
                ->first();

            if (!$order)
                throw new ModelNotFoundException("This order doesn't belong to this customer or this address");

            $bin = Bin::query()
                ->where("id", "=", $request->bin_id)
                ->where("status", "=", "unallocated")
                ->first();

            if (!$bin)
                throw new ModelNotFoundException("This bin doesn't exist or has already been allocated");

            $bin->order_id = $order->id;
            $bin->status = "allocated";
            $bin->save();

            $order->order_status = "accepted";
            $order->save();

            // create customer bin info
            CustomerBin::create([
                "user_id" => $order->user_id,
                "bin_id" => $bin->id,
                "user_address_id" => $request->user_address_id
            ]);

            $admin_notification = AdminNotification::create([
                "user_id" => $request->user_id,
                "description" => "Your bin was dropped to your address"
            ]);

            DB::commit();

            Session::flash("success", "Bin assigned successfully");

            return redirect()->route("bin.info", $bin->qr_code);

        } catch (ValidationException $validationException) {

            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());

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
     * Decompose bin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function decompose($id): \Illuminate\Http\JsonResponse
    {
        try {
            $bin = Bin::findOrFail($id);

            if ($bin->decomposition_date != null)
                return response()->json(["message" => "This bin has already been decommissioned"]);

            $bin->decomposition_date = Carbon::now()->format("Y-m-d H:i:s");
            $bin->save();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Assign User Or Create Pickup Page by QR
     * @param $qr_code
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function binInfo($qr_code)
    {
        try {
            $customers = null;

            $bin_info = Bin::query()
                ->with(["order"])
                ->where("qr_code", "=", $qr_code)
                ->firstOrFail();

            if (!$bin_info->order) {

                $customers = User::role("Customer")
                    ->select("id", "first_name", "last_name")
                    ->get();
            }

            return view("bin.qr", compact("bin_info", "customers"));
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the bin");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
