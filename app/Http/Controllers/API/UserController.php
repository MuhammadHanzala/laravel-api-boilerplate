<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Validator;

use App\User;
use App\VerifyUser;
use App\PasswordReset;
use App\TwoFACodes;
use App\Mail\EmailVerification;
use App\Mail\ForgotPasswordRequest;
use App\Mail\PasswordResetSuccessful;
use App\Mail\VerifyTwoFa;

class UserController extends Controller
{
    //User Registration

    /**
     * @api {post} /register Register
     * @apiName RegisterUser
     * @apiGroup User
     *
     * @apiParam {String} name Username
     * @apiParam {Email} email Email
     * @apiParam {String} password Password
     * @apiParam {String} c_password Confirm Password
     *
     * @apiExample {js} Response Example:
     * {
     *  "success": {
     *          "message": "We have sent a confirmation mail to your email. Please check your inbox."
     *      }
     *  }
     */
    public function register(Request $request)
    {
        $userDetails = '';
        if ($request->isJson()) {
            $userDetails = $request->json()->all();
        } else {
            $userDetails = $request->all();
        }

        $validator = Validator::make($userDetails, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error["message"] = $errors[0];
            $error["code"] = 'VALIDATION_ERROR';
            return response()->json(["error" => $error], 400);
        }
        $userDetails['password'] = bcrypt($userDetails['password']);

        try {
            $user = User::create($userDetails);
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40)
            ]);
            Mail::to($user->email)->send(new EmailVerification($user)); // Send Email for account verification
            $success['message'] = "We have sent a confirmation mail to your email. Please check your inbox.";
            return response()->json(['success' => $success], 200);
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }

    //verifying user account by token, sent via mail
    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser)) {
            $user = $verifyUser->user;
            if (!$user->isVerified) {
                $verifyUser->user->isVerified = 1;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            } else {
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return redirect('/message')->with('warning', "Sorry your email cannot be identified.");
        }

        return redirect('/message')->with('status', $status);
    }


    //User Login

    /**
     * @api {post} /login Login
     * @apiName LoginUser
     * @apiGroup User
     *
     * @apiParam {Email} email Email
     * @apiParam {String} password Password
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "user": {
     *               "id": 1,
     *              "name": "hanzala",
     *              "email": "mhanzala123@gmail.com",
     *              "isVerified": 1,
     *              "created_at": "2018-05-25 05:49:03",
     *              "updated_at": "2018-05-28 05:40:25"
     *              "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImU5ZTI3ZTE3ODIwMmEwMDQ0MjYzM2VhMDc3NGNkNDJiYThmNDg0MmU5NTU3MWQzNmM1ZmRkMGVlZGQ2YzM1MWRhYmNhNzVhZGIyZmRjMTc0In0.eyJhdWQiOiIxIiwianRpIjoiZTllMjdlMTc4MjAyYTAwNDQyNjMzZWEwNzc0Y2Q0MmJhOGY0ODQyZTk1NTcxZDM2YzVmZGQwZWVkZDZjMzUxZGFiY2E3NWFkYjJmZGMxNzQiLCJpYXQiOjE1Mjc0ODYxMzMsIm5iZiI6MTUyNzQ4NjEzMywiZXhwIjoxNTU5MDIyMTMzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.Ie2z4hAToEQoIHOI11aPCb8BzhCemc32VYBOcn6PwJWaVhIV9wHDSvI4mcwIzwpFtNq5PMo_cukoLAfroVWWmdZltpuyx2iciJBEqnNwn08J-3F6x7zWtPWEbs1q9hzlttKNHRi-6_D40tsYFm6G67FfsP75ZTZMl2AmhFddykWQyhwzzsHG8OFLx8FNXrGbpjjLo5nZbYvsV8ABfLITjUc7KewcX8CQonxbo3KqNhWqXO8uFISPT6fa4YV7WMxt7zVScOv8guQhQuBDgY23-dGZWq9-UG_qMDURw47HNhPxE1EI6IoEAlEU39ThIR18on0DEkfcSxziaRwqqJmg50NnivlQMm0BnDGydPuqW8gYWDXEGoFKDzg3O4eXI607X7udJCB7V8WTV6QOmglQ-d0av3PuVpcSCHpF7uA2xHbSYin4AG-MXEDMbgPQA95G6F-OCJyd0Pp5czbRPTsvMl3dBhzqcvZ8d1R9a0hAez2iZ2odWIKcdhegaqgHg3iK0Si44kFtr9kvq9LZHs6OXnih10fXIQpwI5legEhT-PUQbMu22ASg2OtKxiiALrVyFKtU-E2AkxJv6KEe_PNIBmIHbWOn3dR_cL8xNgbv_tIfSB6AD6hHmLJkPw73lViM5nqGnR8nRZQFY6NspZCtYoASnRCH-M3bYZRHSJP__3I",
     *          }
     *      }
     *  }
     */
    public function login(Request $request)
    {
        if (Auth::attempt(["email" => request("email"), "password" => request("password")])) {
            $user = Auth::user();
            if (!$user->isVerified) { //if user account is not verified. Request verification.
                $verifyToken = $user->verifyUser->token;
                Mail::to($user->email)->send(new EmailVerification($user));
                $error['message'] = "Your Email is not verified, we have sent a confirmation mail to your email. Please check your inbox.";
                $error['code'] = "NOT_VERIFIED";
                return response()->json(['error' => $error], 400);
            } else {
                if ($user["2fa"]) {
                    $code = TwoFACodes::create(["user_id" => $user["id"], "code" => substr(uniqid(rand(), true), 16, 7)]);
                    $user["code"] = $code["code"];
                    Mail::to($user->email)->send(new VerifyTwoFa($user));
                    $error['message'] = "Please verify Two-factor Authentication. We have sent a code to your email.";
                    $error['code'] = "VERIFY_2FA";
                    return response()->json(['error' => $error], 401);
                }
                $success['user'] = $user;
                $success["user"]["token"] = $user->createToken($user->name)->accessToken;
                return response()->json(['success' => $success], 200);
            }
        } else {
            $user = User::where('email', $request->email)->first();
            if (isset($user)) {
                $error['message'] = "The password you have entered is incorrect.";
                $error['code'] = "AUTHENTICATION_ERROR";
                return response()->json(['error' => $error], 400);
            }
            $error['message'] = "The email you have entered is incorrect.";
            $error['code'] = "AUTHENTICATION_ERROR";
            return response()->json(['error' => $error], 400);
        }
    }



    //User forgot password, send a token via mail

    /**
     * @api {post} /user/forgot-password-request Forgot Password
     * @apiName ForgotPassword
     * @apiGroup User
     *
     * @apiParam {Email} email Email
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "message": "We have sent instructions to your email for reset password. Please check your inbox."
     *      }
     *  }
     */
    public function ForgotPasswordRequest(Request $request)
    {
        //email
        $details = ['email' => $request->email];

        $validator = Validator::make($details, [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error["message"] = $errors[0];
            $error["code"] = 'VALIDATION_ERROR';
            return response()->json(["error" => $error], 400);
        }

        try {
            $passResetToken = PasswordReset::create([
                'email' => $details['email'],
                'token' => str_random(40)
            ]);
            $token = $passResetToken->token;
            Mail::to($passResetToken->email)->send(new ForgotPasswordRequest($token));
            $success['message'] = "We have sent instructions to your email for reset password. Please check your inbox.";
            return response()->json(['success' => $success], 200);
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }

    //Verifying forgot password token via mail
    public function verifyForgotPasswordToken(Request $request, $token)
    {
        $resetToken = PasswordReset::where('token', $token)->first();
        if (isset($resetToken)) {
            // $resetToken["current_date"] = date('Y-m-d H:i:s', strtotime(gmdate("Y-m-d H:i:s")));
            // $resetToken["expiry_date"] = date('Y-m-d H:i:s', strtotime($resetToken["created_at"]) + 86400);
            $expire_within_hours = ((strtotime($resetToken["created_at"]) + (env('REQUEST_EXPIRATION_TIME') * 3600)) - strtotime(gmdate("Y-m-d H:i:s"))) / 3600;
            $request->session()->put(['email' => $resetToken->email]);
            $request->session()->put(['token' => $resetToken->token]);
            $request->session()->put(['expiry' => $expire_within_hours]);
            if ($expire_within_hours < 0) {
                return redirect('/message')->with('warning', "Sorry! This link has been expired.");
            }
            //if token is valid, redirect to reset form
            return view("passwordResetForm", compact('resetToken'));
        }
        return redirect('/message')->with('warning', "Sorry your request could not be found or may have been deleted.");
    }

    //Submit new Password details by form
    public function resetPassword(Request $request)
    {
        $newDetails = $request->all();
        $newDetails["email"] = session('email');
        $token = session('token');
        $validator = Validator::make($newDetails, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $user = User::where('email', $newDetails['email'])->first();
            $user['password'] = bcrypt($newDetails['password']);
            $user->save();
            Mail::to($user->email)->send(new PasswordResetSuccessful());
            PasswordReset::where('token', $token)->delete();
            return redirect('/message')->with('status', 'Your password has been reset successfully');
        }
    }

    //Password Reset by LoggedIn User

    /**
     * @api {post} /user/reset-password Reset Password
     * @apiName ResetPassword
     * @apiGroup User
     *
     * @apiParam {Email} email Email
     * @apiParam {String} current_password Current Password
     * @apiParam {String} password New Password
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "message": "Your password has been reset successfully."
     *      }
     *  }
     */

    public function resetPasswordByAuth(Request $request)
    {
        $newDetails = $request->all();
        $user = Auth::user();
        $validator = Validator::make($newDetails, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
            'current_password' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error["message"] = $errors[0];
            $error["code"] = 'VALIDATION_ERROR';
            return response()->json(["error" => $error], 400);
        } else {
            if (Hash::check($newDetails["current_password"], $user->password)) {
                // The passwords match...
                $user['password'] = bcrypt($newDetails['password']);
                $user->save();
                Mail::to($user->email)->send(new PasswordResetSuccessful());
                return response()->json(["success" => ["message" => "Your password has been reset successfully."]], 200);
            } else {
                $error['code'] = "INVALID_CREDENTIALS";
                $error['message'] = "Your current password is incorrect";
                return response()->json(["error" => $error], 400);
            }
        }
    }


    //User Logout

    /**
     * @api {post} /user/logout Logout
     * @apiName Logout
     * @apiGroup User
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "message": "Logout Successfuly."
     *      }
     *  }
     */
    public function logout(Request $request)
    {
        $request->user()->token()->delete();
        return response()->json(["success" => ["message" => "Logout Successfuly."]], 200);
    }

    //Logout from other devices

    /**
     * @api {post} /user/logout-other-devices Logout From Other Devices
     * @apiName LogoutFromOtherDevices
     * @apiGroup User
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "message": "Successfully Logged Out from other devices."
     *      }
     *  }
     */
    public function revokeAllTokens(Request $request)
    {
        $user = $request->user();
        $currentDeviceTokenId = $request->user()->token()->id;
        $userTokens = $user->tokens;

        foreach ($userTokens as $token) {
            if ($token->id !== $currentDeviceTokenId) {
                $token->revoke();
            }
        }
        return response()->json(["success" => ["token" => "Successfully Logged Out from other devices."]]);
    }

    //Toggle 2fa

    /**
     * @api {post} /user/toggle-2fa Toggle 2fa
     * @apiName Toggle2fa
     * @apiGroup User
     *
     * @apiExample {js} Response Example:
     *  {
     *      "success": {
     *          "id": 2,
     *          "name": "Hanzala",
     *          "email": "hanzasdala12a3dssdasd4443@gmail.com",
     *          "imageUrl": "http://192.168.5.102/schedule-api/public/storage/1/k98uD.png",
     *          "2fa": true,
     *          "isVerified": 1,
     *          "created_at": "2018-06-21 15:00:41",
     *          "updated_at": "2018-06-21 15:00:58"
     *      }
     *  }
     */
    public function toggle2fa(Request $request)
    {
        $user = $request->user();
        $user->update(["2fa" => !$user["2fa"]]);
        return response()->json(["success" => $user]);
    }


    //User Login

    /**
     * @api {post} /verify-2fa/:code Verify 2Fa
     * @apiName Verify2Fa
     * @apiGroup User
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "user": {
     *               "id": 1,
     *              "name": "hanzala",
     *              "email": "mhanzala123@gmail.com",
     *              "isVerified": 1,
     *              "created_at": "2018-05-25 05:49:03",
     *              "updated_at": "2018-05-28 05:40:25"
     *              "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImU5ZTI3ZTE3ODIwMmEwMDQ0MjYzM2VhMDc3NGNkNDJiYThmNDg0MmU5NTU3MWQzNmM1ZmRkMGVlZGQ2YzM1MWRhYmNhNzVhZGIyZmRjMTc0In0.eyJhdWQiOiIxIiwianRpIjoiZTllMjdlMTc4MjAyYTAwNDQyNjMzZWEwNzc0Y2Q0MmJhOGY0ODQyZTk1NTcxZDM2YzVmZGQwZWVkZDZjMzUxZGFiY2E3NWFkYjJmZGMxNzQiLCJpYXQiOjE1Mjc0ODYxMzMsIm5iZiI6MTUyNzQ4NjEzMywiZXhwIjoxNTU5MDIyMTMzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.Ie2z4hAToEQoIHOI11aPCb8BzhCemc32VYBOcn6PwJWaVhIV9wHDSvI4mcwIzwpFtNq5PMo_cukoLAfroVWWmdZltpuyx2iciJBEqnNwn08J-3F6x7zWtPWEbs1q9hzlttKNHRi-6_D40tsYFm6G67FfsP75ZTZMl2AmhFddykWQyhwzzsHG8OFLx8FNXrGbpjjLo5nZbYvsV8ABfLITjUc7KewcX8CQonxbo3KqNhWqXO8uFISPT6fa4YV7WMxt7zVScOv8guQhQuBDgY23-dGZWq9-UG_qMDURw47HNhPxE1EI6IoEAlEU39ThIR18on0DEkfcSxziaRwqqJmg50NnivlQMm0BnDGydPuqW8gYWDXEGoFKDzg3O4eXI607X7udJCB7V8WTV6QOmglQ-d0av3PuVpcSCHpF7uA2xHbSYin4AG-MXEDMbgPQA95G6F-OCJyd0Pp5czbRPTsvMl3dBhzqcvZ8d1R9a0hAez2iZ2odWIKcdhegaqgHg3iK0Si44kFtr9kvq9LZHs6OXnih10fXIQpwI5legEhT-PUQbMu22ASg2OtKxiiALrVyFKtU-E2AkxJv6KEe_PNIBmIHbWOn3dR_cL8xNgbv_tIfSB6AD6hHmLJkPw73lViM5nqGnR8nRZQFY6NspZCtYoASnRCH-M3bYZRHSJP__3I",
     *          }
     *      }
     *  }
     */
    public function verifyTwoFaCode(Request $request, $code)
    {
        try {
            $twoFaCode = TwoFACodes::where("code", $code)->get()->first();
            if (isset($twoFaCode)) {
                $expire_within_hours = ((strtotime($twoFaCode["created_at"]) + (env('REQUEST_EXPIRATION_TIME') * 3600)) -
                        strtotime(gmdate("Y-m-d H:i:s"))) / 3600;
                if ($expire_within_hours < 0) {
                    $twoFaCode->delete();
                    $error['code'] = "CODE_EXPIRED";
                    $error['message'] = "Sorry! This Code has been expired.";
                    return response()->json(["error" => $error], 400);
                } else {
                    $twoFaCode->delete();
                    $user = User::find($twoFaCode["user_id"]);
                    $success['user'] = $user;
                    $success["user"]["token"] = $user->createToken($user->name)->accessToken;
                    return response()->json(['success' => $success], 200);
                }
            } else {
                $error['code'] = "404_NOT_FOUND";
                $error['message'] = "Sorry! This Code could not be found or may have been deleted.";
                return response()->json(["error" => $error], 404);
            }
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }

    /**
     * @api {put} /user/:id Edit User
     * @apiName editUser
     * @apiGroup User
     * @apiParam {string} name Name
     *
     * @apiExample {js} Response Example:
     * {
     *      "success": {
     *          "id": 3,
     *          "name": "Kumail Abbas",
     *          "email": "kumail.abbas@mujadidia.com",
     *          "imageUrl": null,
     *          "2fa": 0,
     *          "isVerified": 1,
     *          "created_at": "2018-07-02 11:32:41",
     *          "updated_at": "2018-07-02 11:32:41"
     *      }
     * }
     */
    public function edit(Request $request, $id)
    {
        try {
            $data = '';
            if ($request->isJson()) {
                $data = $request->json()->all();
            } else {
                $data = $request->all();
            }
            $validator = Validator::make($data, [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $error["message"] = $errors[0];
                $error["code"] = 'VALIDATION_ERROR';
                return response()->json(["error" => $error], 400);
            }
            $user = User::findOrFail($id);
            $user->name = $data["name"];
            $user->save();
            return response()->json(["success" => $user], 200);
        } catch (QueryException $exception) {
            return response()->json($exception, 400);
        }
    }
}
