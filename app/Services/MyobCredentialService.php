<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use App\Models\MyobCredential;

class MyobCredentialService
{
    /**
     * Get latest MYOB credentials
     */
    public function getMyobCredentials()
    {
        try {
            $myob_credential = MyobCredential::firstOrFail();

            $current_time = Carbon::now();
            $updated_time = Carbon::parse($myob_credential->updated_at);

            $diff_in_seconds = $updated_time->diffInSeconds($current_time);

            if ($diff_in_seconds >= 1200) {
                $client_id = env("MYOB_API_KEY");
                $client_secret = env("MYOB_API_SECRET");
                $url ="https://secure.myob.com/oauth2/v1/authorize";
                $query="client_id=$client_id&client_secret=$client_secret&grant_type=refresh_token&refresh_token={$myob_credential->refresh_token}";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                $response = curl_exec($ch);
                curl_close($ch);

                $credentials = json_decode($response, true);

                $myob_credential->access_token = $credentials["access_token"];
                $myob_credential->save();
            }

            return $myob_credential;
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find any MYOB data. Make sure you've connected to your MYOB account");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
