<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class SendSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobile_number, $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobile_number, $message)
    {
        $this->mobile_number = $mobile_number;
        $this->message = $message;
    }

    /**
     * Send SMS Notification
     * @return void
     */
    public function handle()
    {
        try {
            $twilio_sid = env("TWILIO_SID");
            $twilio_auth_token = env("TWILIO_AUTH_TOKEN");
            $twilio_number = env("TWILIO_NUMBER");

            $client_number = "+977{$this->mobile_number}";

            $client = new Client($twilio_sid, $twilio_auth_token);

            $client->messages->create(
                $client_number,
                [
                    "from" => $twilio_number,
                    "body" => $this->message
                ]
            );
        } catch (ConfigurationException $configurationException) {
            Log::error($configurationException->getMessage());
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
