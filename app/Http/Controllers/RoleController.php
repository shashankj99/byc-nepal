<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @var Role
     */
    protected $role;

    /**
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Get all roles
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $roles = $this->role->all();
            return view("role.index")
                ->with(["roles" => $roles, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create a new role
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "name" => "required|unique:roles,name"
            ]);

            $this->role->create(["name" => ucfirst($request->name)]);

            return redirect()->route("role");
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
     * Get role details
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $role = $this->role->findOrFail($id);
            return view("role.edit")
                ->with(["role" => $role]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the role");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Edit a role
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "name" => "required|unique:roles,name,".$id
            ]);
            $role = $this->role->findOrFail($id);
            $role->name = ucfirst($request->name);
            $role->save();
            return redirect()->route("role");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the role");
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
     * Method to delete the role
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $role = $this->role->findOrFail($id);
            $role->delete();
            return response()->json(["message" => "success"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }
}
