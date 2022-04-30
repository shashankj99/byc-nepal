<?php

namespace App\Http\Controllers;

use App\Jobs\SyncMyobSuppliers;
use App\Jobs\SyncSupplierRefunds;
use App\Models\Tool;
use App\Services\MyobCredentialService;
use Illuminate\Support\Facades\Session;

class MyobController extends Controller
{
    /**
     * @var MyobCredentialService
     */
    protected $myobCredential;

    /**
     * @param MyobCredentialService $myobCredential
     */
    public function __construct(MyobCredentialService $myobCredential)
    {
        $this->myobCredential = $myobCredential;
    }

    /**
     * Method to sync customers between app and myob
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function syncCustomer()
    {
        try {
            $myob_credential = $this->myobCredential->getMyobCredentials();

            $headers = array(
                'Authorization: Bearer '.$myob_credential->access_token.'',
                'x-myobapi-key: '.env("MYOB_API_KEY").'',
                'x-myobapi-version: v2'
            );

            $this->handleToolStatus("User");

            dispatch(new SyncMyobSuppliers("sync", "User", $headers));

            Session::flash("info", "Syncing has started");
            return redirect()->route("tools");
        } catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Sync Customer Records
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function syncCustomerRefunds()
    {
        try {
            $myob_credential = $this->myobCredential->getMyobCredentials();

            $headers = array(
                'Authorization: Bearer '.$myob_credential->access_token.'',
                'x-myobapi-key: '.env("MYOB_API_KEY").'',
                'x-myobapi-version: v2'
            );

            $this->handleToolStatus("MyobTransaction");

            dispatch(new SyncSupplierRefunds("sync", "MyobTransaction", $headers));

            Session::flash("info", "Syncing has started");
            return redirect()->route("tools");
        } catch (\Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Handle tool status
     * @param $model
     * @return void
     */
    private function handleToolStatus($model)
    {
        $tool = Tool::where("service", "=", "sync")
            ->where("model", "=", $model)
            ->first();

        if ($tool) {
            $tool->status = "pending";
            $tool->save();
        } else {
            Tool::create([
                "service" => "sync",
                "model" => $model,
                "status" => "pending"
            ]);
        }
    }
}
