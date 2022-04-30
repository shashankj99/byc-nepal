<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationMailJob;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{

    public function viewVerificationPage()
    {
        return view("auth.verify");
    }

    public function verify(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "token" => "required"
            ]);

            // get token
            $token = VerificationToken::where("tokens", "=", $request->token)
                ->where("type", "=", "registration")
                ->where("status", "=", "active")
                ->firstOrFail();

            // activate the user
            $user = $token->user;
            $user->status = "active";
            $user->save();

            // delete the token
            $token->delete();

            return redirect()->route("email.verify");
        } catch (ValidationException $validationException) {
            Session::flash("error", $validationException->getMessage());
            return redirect()->route("email.verify");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "This token is expired");
            return redirect()->route("email.verify");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->route("email.verify");
        }
    }

    /**
     * Resend verification mail
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = auth()->user();

            $user->verificationTokens()->delete();

            $token = $user->verificationTokens()->create([
                "tokens" => base64_encode(Str::uuid()),
                "type" => "registration",
                "status" => "active"
            ]);

            dispatch(new SendRegistrationMailJob($token->tokens, $user->email));

            Session::flash("success", "Please check your email for verification link");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
