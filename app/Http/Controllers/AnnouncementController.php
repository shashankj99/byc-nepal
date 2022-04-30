<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    /**
     * View all announcements
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $announcements = Announcement::all();
            return view("announcement.index")
                ->with(["announcements" => $announcements, 'i' => 1]);
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show create announcement page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            return view("announcement.create");
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Create new announcement.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "heading" => "required",
                "description" => "required",
                "publish_from" => "required|date",
                "publish_to" => "required|date|after_or_equal:publish_from",
                "image" => "sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
                "status" => "required|in:active,inactive"
            ]);

            $request_body = $request->only(
                "heading", "sub_heading", "description", "publish_from", "publish_to", "image", "status"
            );

            if (isset($request_body["image"]) && $request_body["image"])
                $request_body["image"] = $this->storeImage(lcfirst($request_body["heading"]), $request_body["image"]);

            $request_body["heading"] = ucfirst($request_body["heading"]);
            $request_body["sub_heading"] = ucfirst($request_body["sub_heading"]);

            Announcement::create($request_body);

            Session::flash("success", "Announcement created successfully");

            return redirect()->route("announcement");
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
     * view announcement detail
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            return view("announcement.edit")
                ->with(["announcement" => $announcement]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the announcement");
            return redirect()->back();
        } catch (\Exception $exception) {
            Session::flash("error", $exception->getMessage());
            return redirect()->back();
        }
    }

    public function update($id, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "heading" => "required",
                "description" => "required",
                "publish_from" => "required|date",
                "publish_to" => "required|date|after_or_equal:publish_from",
                "image" => "sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
                "status" => "required|in:active,inactive"
            ]);

            $request_body = $request->only(
                "heading", "sub_heading", "description", "publish_from", "publish_to", "image", "old_image", "status"
            );

            $announcement = Announcement::findOrfail($id);

            if (isset($request_body["old_image"])) {
                if (isset($request_body["image"])) {
                    $image_path = public_path("images/announcements/{$request_body['old_image']}");
                    if (file_exists($image_path))
                        unlink($image_path);
                    $request_body["image"] = $this->storeImage(
                        lcfirst($request_body["heading"]), $request_body["image"]
                    );
                } else $request_body["image"] = $request_body["old_image"];
            } else {
                if (isset($request["image"])) {
                    $request_body["image"] = $this->storeImage(
                        lcfirst($request_body["heading"]), $request_body["image"]
                    );
                }
            }

            $request_body["heading"] = ucfirst($request_body["heading"]);
            $request_body["sub_heading"] = ucfirst($request_body["sub_heading"]);

            unset($request_body["old_image"]);

            $announcement->update($request_body);

            Session::flash("success", "Announcement updated successfully");

            return redirect()->route("announcement");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Session::flash("error", "Unable to find the announcement");
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
     * delete announcement
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        try {
            $announcement = Announcement::findOrFail($id);

            if ($announcement->image) {
                $image_path = public_path("images/announcements/{$announcement->image}");

                if (file_exists($image_path))
                    unlink($image_path);
            }

            $announcement->delete();

            return response()->json(["message" => "Successful"]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json([], 404);
        } catch (\Exception $exception) {
            return response()->json([], 500);
        }
    }

    /**
     * Store image
     * @param $heading
     * @param $image
     * @return string
     */
    private function storeImage($heading, $image): string
    {
        //slugify heading
        $heading = Str::slug($heading);

        // generate a random string
        $image_name = $heading.Str::uuid();

        // get extension from image
        $extension = $image->extension();

        // new image name with extension
        $new_image_name = "$image_name.$extension";

        // move image to images => announcements folder
        $image->move(public_path("images/announcements"), $new_image_name);

        return $new_image_name;
    }
}
