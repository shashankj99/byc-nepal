<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\MyobCredential;
use App\Models\MyobTransaction;
use App\Models\Order;
use App\Models\User;
use App\Services\MyobCredentialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    protected $myobCredentialService;

    public function __construct(MyobCredentialService $myobCredentialService)
    {
        $this->myobCredentialService = $myobCredentialService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->hasRole('Customer')) {
                $id = $user->id;

                $customer = User::with(["userAddresses" => function ($q) {
                    $q->select("id", "user_id", "address")
                        ->where("is_default", "=", "1");
                }, "orders"])
                    ->select("id", "password", "myob_uid")
                    ->findOrFail($id);

                if (Hash::check("Byc@1234", $customer->password))
                    Session::flash(
                        "warning",
                        "You must change your password the first time you login."
                    );

                $today_date = Carbon::now()->format("Y-m-d H:i:s");

                $announcements = Announcement::where("publish_from", "<=", $today_date)
                    ->where("publish_to", ">=", $today_date)
                    ->where("status", "=", "active")
                    ->get();

                $total_orders_count = $customer->orders()->count("id");

                $pending_orders_count = $customer->orders()->where("order_status", "=", "pending")
                    ->count("id");

                if ($pending_orders_count > 0)
                    Session::flash(
                        "info",
                        "Your $pending_orders_count out of $total_orders_count bin(s) are awaiting approval"
                    );

                $current_balance = MyobTransaction::query()
                    ->where("user_id", "=", $id)
                    ->sum("amount");

                return view('home')
                    ->with([
                        "customer" => $customer,
                        'announcements' => $announcements,
                        "current_balance" => $current_balance
                    ]);
            } else if ($user->hasRole('Driver')) {
                if (Hash::check("Byc@1234", $user->password))
                    Session::flash(
                        "warning",
                        "You must change your password the first time you login."
                    );

                return view("home")
                    ->with(["user" => auth()->user()]);
            } else {
                $show_myob_div = false;

                $myob_credentials = MyobCredential::first();

                if (!$myob_credentials)
                    $show_myob_div = true;

                if (isset($request->code) && $request->code) {
                    $redirect_url = env("APP_URL")."/dashboard";
                    $client_id = env("MYOB_API_KEY");
                    $client_secret = env("MYOB_API_SECRET");
                    $code = $request->code;

                    $url ="https://secure.myob.com/oauth2/v1/authorize";
                    $query="client_id=$client_id&client_secret=$client_secret&scope=CompanyFile&code=$code&redirect_uri=$redirect_url&grant_type=authorization_code";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $newarray1 = json_decode($response, true);

                    if (isset($newarray1["access_token"]) && isset($newarray1["refresh_token"])) {
                        if ($myob_credentials) {
                            $myob_credentials->access_token = $newarray1["access_token"];
                            $myob_credentials->refresh_token = $newarray1["refresh_token"];
                            $myob_credentials->uid = $newarray1["user"]["uid"];
                        } else
                            MyobCredential::create([
                                "access_token" => $newarray1["access_token"],
                                "refresh_token" => $newarray1["refresh_token"],
                                "uid" => $newarray1["user"]["uid"]
                            ]);

                        $show_myob_div = false;
                    }
                }

                $total_active_customers = User::role("Customer")
                    ->whereNull("off_board_at")
                    ->where("status", "=", "active")
                    ->count("id");

                $total_off_board_customers = User::role("Customer")
                    ->whereNotNull("off_board_at")
                    ->where("status", "=", "active")
                    ->count("id");

                $total_orders = Order::count("id");

                $total_drivers = User::role("Driver")
                    ->whereNull("off_board_at")
                    ->where("status", "=", "active")
                    ->count("id");

                $year = Carbon::now()->format("Y");

                return view("home")
                    ->with(
                        [
                            "total_active_customers" => $total_active_customers,
                            "total_off_board_customers" => $total_off_board_customers,
                            "total_orders" => $total_orders,
                            "total_drivers" => $total_drivers,
                            "year" => $year,
                            "show_myob_div" => $show_myob_div
                        ]
                    );
            }
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get Bin Order Statistics
     * @return \Illuminate\Http\JsonResponse
     */
    public function chartStats(): \Illuminate\Http\JsonResponse
    {
        try {
            $this_year = Carbon::now()->format("Y");
            $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $data = [];

            $orders = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%m') as 'month', COUNT(*) as 'total_orders'")
            )
                ->whereYear("created_at", "=", $this_year)
                ->orderBy("total_orders", "desc")
                ->groupBy("month")
                ->get();

            foreach ($orders as $order) {
                $month = (int) $order->month;
                $data[$month] = $order->total_orders;
            }

            foreach ($months as $month) {
                if (isset($data[$month]) && $data[$month])
                    continue;
                else
                    $data[$month] = 0;
            }

            ksort($data);

            return response()->json(["data" => array_values($data), "threshold" => max($data)]);
        } catch (\Exception $exception) {
            return response()->json(["error" => $exception->getMessage()], 500);
        }
    }
}
