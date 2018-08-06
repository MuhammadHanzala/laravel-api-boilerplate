<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use Validator;
use App\Image;
use App\User;

class ImageController extends Controller
{
    //
    /**
     * @api {post} /user/image Add User Image
     * @apiName AddUserImage
     * @apiGroup User
     *
     * @apiParam {File} file Image File (required)
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "id": 1,
     *          "name": "hanzala",
     *         "email": "hanzala@gmail.com",
     *          "imageUrl": "http://192.168.5.102/storage/8/download.jpg",
     *          "isVerified": 1,
     *          "created_at": "2018-06-12 09:58:28",
     *          "updated_at": "2018-06-12 10:21:09"
     *      }
     *  }
     */
    public function addUserImage(Request $request)
    {
        $user = Auth::user();
        $imageDetails = '';
        if ($request->isJson()) {
            $imageDetails = $request->json()->all();
        } else {
            $imageDetails = $request->all();
        }

        $validator = Validator::make($imageDetails, [
            'url' => 'required_without_all:file|url',
            'file' => 'required_without_all:url|image|max:20000'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error["message"] = $errors[0];
            $error["code"] = 'VALIDATION_ERROR';            
            return response()->json(["error" => $error], 400);
        }

        try {
            if(isset($imageDetails["url"])){
                $user->addMediaFromUrl($imageDetails["url"])
                     ->usingName($user["name"]."'s dp")
                     ->toMediaCollection();
            }else{
                $file = $request->file('file');
                $user->addMedia($file)
                     ->usingName($user["name"]."'s dp") 
                     ->toMediaCollection();
            }
            $image = $user->getMedia()->last();
            $extension = explode('/',$image->mime_type);
            $image->file_name = str_random(5).'.'. $extension[1];
            $image->save();
            $user["imageUrl"] = $image->getFullUrl();
            $user->save();
            unset($user->{"media"});
            return response()->json(["success" => $user], 200);
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }
}
