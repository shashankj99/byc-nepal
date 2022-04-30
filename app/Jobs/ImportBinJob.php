<?php

namespace App\Jobs;

use App\Models\Bin;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportBinJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $service, $model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($service, $model)
    {
        $this->service = $service;
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = public_path("files/uploads/Bin.csv");
        if (file_exists($file)) {
            $row = 0;
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;

                    if ($row == 1)
                        continue;

                    $bin_exists = Bin::where("bin_number", "=", $data[1])
                        ->orWhere("qr_code", "=", $data[2])
                        ->first();

                    if ($bin_exists)
                        continue;

                    Bin::create([
                        "order_id" => ($data[0] == "") ? null : $data[0],
                        "bin_number" => $data[1],
                        "qr_code" => $data[2],
                        "bin_type" => $data[3],
                        "status" => $data[4],
                        "decomposition_date" => ($data[5] == "" || $data[5] == null) ? null : Carbon::parse($data[5])->format("Y-m-d H:i:s")
                    ]);
                }
                fclose($handle);
            }
        }
    }
}
