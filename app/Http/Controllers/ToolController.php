<?php

namespace App\Http\Controllers;

use App\Jobs\ImportBinJob;
use App\Jobs\ImportCustomerJob;
use App\Models\Tool;
use App\Services\MyobCredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class ToolController extends Controller
{
    /**
     * @var MyobCredentialService
     */
    protected $myobCredentialService;

    /**
     * @param MyobCredentialService $myobCredentialService
     */
    public function __construct(MyobCredentialService  $myobCredentialService)
    {
        $this->myobCredentialService = $myobCredentialService;
    }

    /**
     * View Tools Page
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showToolsPage(Request $request)
    {
        try {
            $import_user_flag = Tool::query()
                ->select("status")
                ->where("service", "=", "import")
                ->where("model", "=", "User")
                ->first();

            $import_bin_flag = Tool::query()
                ->select("status")
                ->where("service", "=", "import")
                ->where("model", "=", "Bin")
                ->first();

            $sync_customers = Tool::query()
                ->select("status")
                ->where("service", "=", "sync")
                ->where("model", "=", "User")
                ->first();

            $sync_myob_transactions = Tool::query()
                ->select("status")
                ->where("service", "=", "sync")
                ->where("model", "=", "MyobTransaction")
                ->first();

            $uid = "";

            if (isset($request->myob_username) && $request->myob_username) {

                $myob_credential = $this->myobCredentialService->getMyobCredentials();

                $headers = array(
                    'Authorization: Bearer '.$myob_credential->access_token.'',
                    'x-myobapi-key: '.env("MYOB_API_KEY").'',
                    'x-myobapi-version: v2'
                );

                $url = "https://arl2.api.myob.com/accountright/57ad5206-5b32-4b1a-9e2e-f82668fbe115/Contact/Supplier";

                $suppliers_first = $this->returnCurlResponse($url, $headers);

                if (isset($suppliers_first["Items"]) && !empty($suppliers_first["Items"])) {
                    $total = $suppliers_first["Count"] ?? 0;
                    $uid = $this->getSupplierUid($suppliers_first["Items"], $request->myob_username);

                    if ($uid == "") {
                        if ($total > 400) {
                            for ($skip=400; $skip <= $total; $skip+=400) {
                                $url = "https://arl2.api.myob.com/accountright/57ad5206-5b32-4b1a-9e2e-f82668fbe115/Contact/Supplier?".'$top=400&$skip='.$skip;
                                $suppliers = $this->returnCurlResponse($url, $headers);
                                if (isset($suppliers["Items"]) && !empty($suppliers["Items"])) {
                                    $uid = $this->getSupplierUid($suppliers["Items"], $request->myob_username);
                                }
                            }
                        }
                    }
                }

            }

            return view(
                "tools",
                compact(
                    "import_user_flag", "import_bin_flag", "sync_customers", "sync_myob_transactions",
                    "uid"
                )
            );
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * dispatch job to import customer
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importCustomers(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["customer_sheet" => "required|mimes:csv,txt"]);
            $customer_sheet = $request->customer_sheet;
            $customer_sheet->move(public_path("files/uploads"), "Customer.csv");
            $this->handleToolStatus("User");
            dispatch(new ImportCustomerJob("import", "User"));
            Session::flash("info", "File import has started and is running on background");
            return redirect()->route("tools");
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
     * dispatch job to import bins
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importBins(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["bin_sheet" => "required|mimes:csv,txt"]);
            $bin_sheet = $request->bin_sheet;
            $bin_sheet->move(public_path("files/uploads"), "Bin.csv");
            $this->handleToolStatus("Bin");
            dispatch(new ImportBinJob("import", "Bin"));
            Session::flash("info", "File import has started and is running on background");
            return redirect()->route("tools");
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
     * Handle tool status
     * @param $model
     * @return void
     */
    private function handleToolStatus($model)
    {
        $tool = Tool::where("service", "=", "import")
            ->where("model", "=", $model)
            ->first();

        if ($tool) {
            $tool->status = "pending";
            $tool->save();
        } else {
            Tool::create([
                "service" => "import",
                "model" => $model,
                "status" => "pending"
            ]);
        }
    }

    /**
     * Make Request to the MYOB server
     * @param $url
     * @param $headers
     * @return mixed
     */
    private function returnCurlResponse($url, $headers)
    {
        $ch12 = curl_init();
        curl_setopt($ch12, CURLOPT_URL, $url);
        curl_setopt($ch12, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch12, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch12, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt($ch12, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt($ch12, CURLOPT_RETURNTRANSFER, 1 );

        $result = curl_exec($ch12);
        curl_close($ch12);
        return json_decode($result, true);
    }

    private function getSupplierUid($items, $search)
    {
        foreach ($items as $item) {

            if (
                (isset($item["FirstName"]) && !empty($item["FirstName"])) &&
                isset($item["LastName"]) && !empty($item["LastName"])
            ) {

                $name = "{$item['FirstName']} {$item['LastName']}";

                if ($name == $search) return $item["UID"];

            } else if (
                (isset($item["FirstName"]) && !empty($item["FirstName"])) &&
                isset($item["LastName"]) && empty($item["LastName"])
            ) {

                if ($item["FirstName"] == $search) return $item["UID"];

            } else {

                if (isset($item["LastName"]) && !empty($item["LastName"]))
                    if ($item["LastName"] == $search) return $item["UID"];

            }

        }

        return "";
    }
}
