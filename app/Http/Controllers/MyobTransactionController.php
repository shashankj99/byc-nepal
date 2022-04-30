<?php

namespace App\Http\Controllers;

use App\Models\MyobTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyobTransactionController extends Controller
{
    /**
     * View customer Refunds
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $myob_transactions = MyobTransaction::query()
                ->with(["user" => function ($q) {
                    $q->with(["customerAccounts" => function ($query) {
                        $query->select("user_id", "account_number")
                            ->where("is_default", "=", "1");
                    }])
                        ->select("id");
                }])
                ->select("id", "user_id", "amount", "payment_date")
                ->where("user_id", "=", auth()->id())
                ->get();
            $i = 1;
            return view("myob_transactions.customer", compact("myob_transactions", "i"));
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
