<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ImportCustomerJob implements ShouldQueue
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
        $file = public_path("files/uploads/Customer.csv");
        if (file_exists($file)) {
            $row = 0;
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;

                    if ($row == 1)
                        continue;

                    $old_user = User::where("mobile_number", "=", $data[2])
                        ->orWhere("email", "=", $data[3])
                        ->first();

                    if ($old_user)
                        continue;

                    DB::beginTransaction();

                    $user = User::create([
                        "first_name" => $data[0],
                        "last_name" => $data[1],
                        "mobile_number" => $data[2],
                        "email" => $data[3],
                        "password" => Hash::make($data[4]),
                        "status" => $data[5],
                        "off_board_at" => ($data[6] == "") ? null : Carbon::parse($data[6])->format("Y-m-d H:i:s"),
                        "myob_uid" => $data[7],
                        "is_admin_created" => "1"
                    ]);

                    // get customer role or create role
                    $role = Role::firstOrCreate(["name" => "Customer"]);

                    // assign role to the user
                    $user->assignRole($role["name"]);

                    // create user's address
                    $user->userAddresses()->create([
                        "address" => $data[8],
                        "type" => ucfirst($data[9]),
                        "suburban" => $data[11],
                        "postal_code" => $data[10],
                        "is_default" => "1"
                    ]);

                    // create customer's account
                    $user->customerAccounts()->create([
                        "account_name" => $data[12],
                        "account_number" => $data[13],
                        "bsb" => $data[14],
                        "bank_name" => $data[15],
                        "branch" => $data[16]
                    ]);

                    DB::commit();
                }
                fclose($handle);
            }
        }
    }
}
