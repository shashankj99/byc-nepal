<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationMailJob;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile_number' => ['required', 'integer', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function showRegistrationForm()
    {
        return view("auth.register");
    }

    public function create(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->validator($request->all())->validate();

            DB::beginTransaction();

            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'mobile_number' => $request['mobile_number'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            // get customer role or create role
            $role = Role::firstOrCreate(["name" => "Customer"]);

            // assign role to the user
            $user->assignRole($role["name"]);

            // create verification token
            $token = $this->createVerificationToken($user);

            DB::commit();

            Auth::login($user);

            dispatch(new SendRegistrationMailJob($token["tokens"], $user->email));

            return redirect()->route("email.verify");

        } catch (ValidationException $validationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validationException->errors());
        } catch (\Exception $exception) {
            DB::rollBack();
            Session::flash("error", $exception->getMessage());
            return redirect()->back()
                ->withInput();
        }

    }

    private function createVerificationToken($user)
    {
        // generate a uid
        $uid = Str::uuid();

        // generate an encoded string
        $token = base64_encode($user->email.$uid);

        // store token in DB
        return $user->verificationTokens()->create([
            'tokens' => $token,
            'status' => "active",
            'type' => "registration"
        ]);
    }
}
