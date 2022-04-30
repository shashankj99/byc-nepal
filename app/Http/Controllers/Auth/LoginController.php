<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {

            $request->validate(["email" => "required|string", "password" => "required|string"]);

            $user = User::where("email", "=", $request->email)
                ->orWhere("mobile_number", "=", $request->email)
                ->firstOrFail();

            auth()->login($user);

            return redirect()->route("dashboard");

        } catch (ModelNotFoundException $modelNotFoundException) {

            Session::flash("error", "Unable to find the user");

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
}
