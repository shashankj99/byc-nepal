<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncMyobSuppliers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $service, $model, $headers;

    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($service, $model, $headers)
    {
        $this->service = $service;
        $this->model = $model;
        $this->headers = $headers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = "https://arl2.api.myob.com/accountright/57ad5206-5b32-4b1a-9e2e-f82668fbe115/Contact/Supplier";

        $suppliers_first = $this->returnCurlResponse($url, $this->headers);

        if (isset($suppliers_first["Items"]) && !empty($suppliers_first["Items"])) {
            $total = $suppliers_first["Count"] ?? 0;
            $this->updateMyobUid($suppliers_first["Items"]);

            if ($total > 400) {
                for ($skip=400; $skip <= $total; $skip+=400) {
                    $url = "https://arl2.api.myob.com/accountright/57ad5206-5b32-4b1a-9e2e-f82668fbe115/Contact/Supplier?".'$top=400&$skip='.$skip;
                    $suppliers = $this->returnCurlResponse($url, $this->headers);
                    if (isset($suppliers["Items"]) && !empty($suppliers["Items"])) {
                        $this->updateMyobUid($suppliers["Items"]);
                    }
                }
            }
        }
    }

    /**
     * Update user myob uid
     * @param $items
     * @return void
     */
    private function updateMyobUid($items)
    {
        foreach ($items as $item) {
            if (isset($item["Notes"]) && !empty($item["Notes"])) {
                $id = (int) $item["Notes"];

                $user = User::find($id);

                if ($user) {
                    $user->myob_uid = $item["UID"];
                    $user->save();
                }
            }
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
}
