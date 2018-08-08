<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Validator;
use App\Image;
use App\User;
use App\TwoFACodes;
use App\Mail\VerifyTwoFa;

class SocialLoginController extends Controller
{
    //

    //User Login

    /**
     * @api {post} /social-login Social Login
     * @apiName SocialUser
     * @apiGroup User
     *
     * @apiParam {String} name Name
     * @apiParam {email} email Email
     * @apiParam {url} avatar User Image Url
     * @apiParam {String} uid User Unique Id
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "user": {
     *              "id": 7,
     *              "name": "Hanzala",
     *              "email": "mhanasdzasdala123asdasd@gmail.com",
     *              "imageUrl": "http://192.168.5.102/storage/7/1522382913_958_stylish-boys-cool-profile-pics-dp-for-facebook-whatsapp.jpg",
     *              "isVerified": 0,
     *              "created_at": "2018-06-12 10:14:30",
     *              "updated_at": "2018-06-12 10:14:32",
     *              "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImYxNzllMGE5ZmRjMDc5Y2UwZjhlZWQzNDc5ZDBmNmRhZWNkN2MyYWJiMzY5YTIxMDJlMjAxYWJlOTY0MWNlMTM1NDRkMGJhNjI4MmI5MDI1In0.eyJhdWQiOiIxIiwianRpIjoiZjE3OWUwYTlmZGMwNzljZTBmOGVlZDM0NzlkMGY2ZGFlY2Q3YzJhYmIzNjlhMjEwMmUyMDFhYmU5NjQxY2UxMzU0NGQwYmE2MjgyYjkwMjUiLCJpYXQiOjE1Mjg3OTg2MzAsIm5iZiI6MTUyODc5ODYzMCwiZXhwIjoxNTYwMzM0NjMwLCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.AIM82lV6krAZVMpeqe1Jy0TGMBoBgSIYH6VaiLA2pAzP-ydhRTjF8hVOQMGP4dt_YA695JpeDzu-iVSJqXtYF9lXC2Cu4zBuBgjvbx6vkPtKQGgj5Y04bl5GXIa8UgWIgzwaHCS49_6GsNM92SBn8Bh5TxbfEjzZdPetOR51pnrYvsXfViVIbp1FG-u8oja7R-vOQDu3b7B8NtWUr2F7QdxEeoXfV5hiHoiHvfHbl8j_zR07nTalLkdyBFukPuX5jAmUCdh6pZU98zJ85voe3RwMUhtkVrNelGkk03pLKFyJC0oYfcSfaobDqCvl16LVbumrvTsLNRYJVv_2dHtlfmIvYBfcIFAPnF1W3WD1PI4r0QQ8L9tX64SLGbOI8CcuUL1MV9TPVmIkAmrzfql7XH0s21ubDdudyBzpHSG3_MPI2yANxVPTEnY7xyI-sZBk3Kl1lDlhCgxmlHmZaSZsAY7p9GgxiskG8VYYPZ8Je7BPMqeRd0xvwNmfX14twzVUaaRsIOo04g7g8ndHNCw18ovNxY9cjRGBSr4v6AwzhwY9-op1VoEQ5rziEas1zWHVzWHWH-QOffl0xT7-TiyhPA6W9bzokTxPxnZe9SziA5uFDnWjKP1inuTNPCCsTnYk2HcVRnJUuJy46o9jaY24rr2dy28VO5EMzRzXMm10v54"
     *          }
     *      }
     *  }
     */
    public function socialLogin(Request $request){
        $userDetails = '';
        if ($request->isJson()) {
            $userDetails = $request->json()->all();
        } else {
            $userDetails = $request->all();
        }
        $validator = Validator::make($userDetails, [
            'name' => 'required',
            'email' => 'required|email',
            'avatar' => 'sometimes|url',
            'uid' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error["message"] = $errors[0];
            $error["code"] = 'VALIDATION_ERROR'; 
            return response()->json(["error" => $error], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if(isset($user)){
                if($user["2fa"]){
                    $code = TwoFACodes::create(["user_id" => $user["id"], "code" => substr(uniqid(rand(), true), 16, 7)]);
                    $user["code"] = $code["code"];
                    Mail::to($user->email)->send(new VerifyTwoFa($user));
                    $error['message'] = "Please verify Two-factor Authentication. We have sent a code to your email.";
                    $error['code'] = "VERIFY_2FA";
                    return response()->json(['error' => $error], 401);
                }
                Auth::login($user);
                $success['user'] = $user;
                $success["user"]["token"] = $user->createToken($user->name)->accessToken;
                return response()->json(['success' => $success], 200);
            }
            $details = [
                "name" => $userDetails["name"],
                "email" => $userDetails["email"],
                "password" => bcrypt($userDetails["uid"])
            ];
            $user = User::create($details);
            if(isset($userDetails["avatar"])){
                $user->addMediaFromUrl($userDetails["avatar"])
                     ->usingName($userDetails["name"]."'s dp")
                     ->toMediaCollection();
                $image = $user->getMedia()->last();
                $extension = explode('/',$image->mime_type);
                $image->file_name = str_random(5).'.'. $extension[1];
                $image->save();
                $user["imageUrl"] = $image->getFullUrl();
                $user->isVerified = 1;
                $user->save();
            }
            $success['user'] = $user;
            $success["user"]["token"] = $user->createToken($user->name)->accessToken;
            unset($user->{"media"});
            return response()->json(['success' => $success], 200);
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }
}
