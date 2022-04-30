<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetPasswordLinkJob;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\VerificationToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * View change password form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function viewChangePasswordForm()
    {
        try {
            return view("auth.passwords.change");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update the logged in user's password
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "password" => "required|min:8|confirmed"
            ]);

            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->is_admin_created = "0";
            $user->save();

            Session::flash("success", "Your password was changed successfully");
            return redirect()->route("dashboard");

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
     * Get addresses of a user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddresses($id): \Illuminate\Http\JsonResponse
    {
        try {
            $userAddresses = UserAddress::where("user_id", "=", $id)
                ->select("id", "address")
                ->get()->toArray();

            return response()->json(["data" => $userAddresses]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Send reset password link to user email address
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetPasswordLink(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "email" => "required|email"
            ]);

            $user = User::with(["verificationTokens" => function($q) {
                $q->where("type", "=", "reset-password")
                    ->where("status", "=", "active");
            }])
                ->where("email", "=", $request->email)
                ->firstOrFail();

            if ($user->verificationTokens->count() > 0)
                $user->verificationTokens()->delete();

            $token = $user->verificationTokens()->create([
                "tokens" => base64_encode(Str::uuid()),
                "status" => "active",
                "type" => "reset-password"
            ]);

            dispatch(new SendResetPasswordLinkJob($token->tokens, $user->email));

            Session::flash("success", "A reset password link was sent to your email");
            return redirect()->back();
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash('error', "Unable to find the user");
            return redirect()->back();
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
     * Verify token and redirect to change password form
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyResetLink(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "token" => "required"
            ]);

            $token = VerificationToken::where("tokens", "=", $request->token)
                ->where("type", "=", "reset-password")
                ->where("status", "=", "active")
                ->firstOrFail();

            return redirect()->route("reset.password.form", ["token" => $token->tokens]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the token");
            return redirect()->route("reset");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->route("reset");
        }
    }

    /**
     * Show Change Password form
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPasswordChangeForm(Request $request)
    {
        try {
            return view("auth.passwords.forgot")
                ->with(["token" => $request->token]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Reset password
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "password" => "required|min:8|confirmed",
                "token" => "required"
            ]);

            $verification_token = VerificationToken::with("user")
                ->where("tokens", "=", $request->token)
                ->where("type", "=", "reset-password")
                ->where("status", "=", "active")
                ->firstOrFail();

            $verification_token->user->password = Hash::make($request->password);
            $verification_token->user->save();

            auth()->login($verification_token->user);

            $verification_token->delete();

            return redirect()->route("dashboard");
        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }
}
