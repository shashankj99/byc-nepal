<?php

namespace App\Providers;

use App\Models\Tool;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::after(function (JobProcessed $event) {
            $command = unserialize($event->job->payload()["data"]["command"]);

            $tool = Tool::where("service", "=", $command->service)
                ->where("model", "=", $command->model)
                ->first();

            if ($tool) {
                $tool->status = "completed";
                $tool->save();
            }

            $customer_file = public_path("files/uploads/Customer.csv");
            $bin_file = public_path("files/uploads/Bin.csv");

            if (file_exists($customer_file))
                unlink($customer_file);

            if (file_exists($bin_file))
                unlink($bin_file);
        });
    }
}
