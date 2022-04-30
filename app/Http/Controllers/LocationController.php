<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // get auth user
            $user = auth()->user();

            // initialize an empty array
            $all_users = [];

            // check if user is admin
            if ($user->hasRole("Admin"))
                $all_users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                    ->where('status', "=", "active")
                    ->get()
                    ->toArray();

            $user_id = $user->id;

            // else get user id from request
            if (isset($request->user_id) && $request->user_id)
                $user_id = $request->user_id;

            // get all location of the user
            $locations = UserAddress::where("user_id", "=", $user_id)
                ->get()
                ->toArray();

            return view("location.index", [
                "users" => $all_users, "locations" => $locations, "i" => 1, "user_id" => $user_id
            ]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Add User address form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        if (auth()->user()->hasRole("Admin"))
            $users = User::role("Customer")->select("id", "first_name", "last_name", "created_at")
                ->where('status', "=", "active")
                ->get()
                ->toArray();
        else $users = [];

        return view("location.create")
            ->with(["postal_codes" => $this->postalCodes(), "users" => $users]);
    }

    /**
     * Store user address
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "address" => "required",
                "address_key" => "required",
                "type" => "required|in:Residential,Business",
                "postal_code" => "required|integer"
            ]);

            $postal_codes = $this->postalCodes();

            if (!in_array($request->postal_code, $postal_codes))
                throw ValidationException::withMessages([
                    "postal_code" => "The given value is invalid"
                ]);

            // get authenticated user id
            $user_id = Auth::id();

            // if request has user id, then change the auth id to the particular user
            if (isset($request->user_id) && $request->user_id)
                $user_id = $request->user_id;

            // check if user has default location
            $check_if_user_has_default_address = $this->checkIfUserHasDefaultLocation($user_id);
            $is_default = $check_if_user_has_default_address ? "0" : "1";

            $api_key = config("app.google_place_api_key");
            $url = config("app.google_place_detail_api_url");

            $result = Http::get($url, [
                "place_id" => $request->address_key,
                "key" => $api_key
            ]);

            // decoded result
            $decoded_result = json_decode($result, true);

            $request_body = $request->only("address", "type");
            $request_body["user_id"] = $user_id;
            $request_body["is_default"] = $is_default;

            if ($decoded_result["status"] == "OK")
                $request_body["suburban"] = $decoded_result["result"]["vicinity"];
            else
                $request_body["suburban"] = null;

            $request_body["postal_code"] = $request->postal_code;

            // create user address
            UserAddress::create($request_body);

            return redirect()->route("location");
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
     * Set an address as the default location
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeDefaultLocation($id): \Illuminate\Http\JsonResponse
    {
        try {
            $user_address = UserAddress::where("id", "=", $id)
                ->firstOrFail();

            $user_address->update(["is_default" => "1"]);

            UserAddress::where("id", "!=", $id)
                ->update(["is_default" => "0"]);

            return response()->json(["message" => "Successful"], 200);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json(["message" => "Unable to find the address"], 404);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 404);
        }
    }

    /**
     * delete an address of a particular address
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $user_address = UserAddress::where("id", "=", $id)
                ->firstOrFail();

            $user_address->delete();

            return response()->json(["message" => "Successful"], 200);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json(["message" => "Unable to find the address"], 404);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 404);
        }
    }

    /**
     * @description Method to get address from google api
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddressFromGoogleApi(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $google_place_api_url = config("app.google_place_api_url");

            $result = Http::get($google_place_api_url, [
                "input" => $request->q,
                "types" => "geocode",
                "key" => config("app.google_place_api_key")
            ]);

            $decoded_result = json_decode($result->body(), true);

            $predictions = $decoded_result["predictions"];

            $places = []; $places_key = [];

            foreach ($predictions as $key => $prediction) {
                $places[$key]['label'] = $places[$key]["value"] = $prediction["description"];
                $places_key[$key]["place_id"] = $prediction["place_id"];
            }

            return response()->json(["places" => $places, "places_key" => $places_key]);
        } catch (RequestException $requestException) {
            return response()->json([], $requestException->getCode());
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Get user addressed by user id
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAddresses($user_id): \Illuminate\Http\JsonResponse
    {
        try {
            $user_addresses = UserAddress::query()
                ->select("id", "user_id", "address")
                ->where("user_id", "=", $user_id)
                ->get();

            return response()->json(["data" => $user_addresses]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Check if user has a default location
     * @param $user_id
     * @return mixed
     */
    private function checkIfUserHasDefaultLocation($user_id)
    {
        return UserAddress::where("user_id", "=", $user_id)
            ->where("is_default", "=", "1")
            ->first();
    }

    /**
     * Skip postal codes
     * @return int[]
     */
    private function skipPostalCodes() : array
    {
        return [
            5001, 5002, 5003, 5004, 5017, 5026, 5027, 5028, 5029, 5030, 5036, 5049, 5053, 5054, 5055, 5056, 5057,
            5058, 5059, 5060, 5077, 5078, 5079, 5080, 5099, 5100, 5101, 5102, 5103, 5104, 5105, 5119, 5122, 5123,
            5124
        ];
    }

    /**
     * return postal codes
     * @return array
     */
    private function postalCodes() : array
    {
        $skip_postal_codes = $this->skipPostalCodes();

        $postal_codes = [];

        for ($i = 5000; $i <= 5127; $i++) {
            if(in_array($i, $skip_postal_codes))
                continue;
            $postal_codes[] = $i;
        }

        $postal_codes[] = 5351;
        $postal_codes[] = 5352;
        $postal_codes[] = 5371;
        $postal_codes[] = 5372;
        $postal_codes[] = 5400;
        $postal_codes[] = 5501;
        $postal_codes[] = 5502;
        $postal_codes[] = 5950;

        return $postal_codes;
    }
}
