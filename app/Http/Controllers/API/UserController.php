<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Validator;

use App\User;
use App\VerifyUser;
use App\PasswordReset;
use App\Mail\EmailVerification;
use App\Mail\ForgotPasswordRequest;
use App\Mail\PasswordResetSuccessful;

class UserController extends Controller
{
    //User Registration
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
            return response()->json(["error" => $errors[0]], 400);
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
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->isVerified) {
                $verifyUser->user->isVerified = true;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            }else{
                $status = "Your e-mail is already verified. You can now login.";
            }
        }else{
            return redirect('/message')->with('warning', "Sorry your email cannot be identified.");
        }

        return redirect('/message')->with('status', $status);
    }

    //User Login
    public function login(Request $request)
    {
      if (Auth::attempt(["email" => request("email"), "password" => request("password")])) {
        $user = Auth::user();
        if(!$user->isVerified){ //if user account is not verified. Request verification.
          $verifyToken = $user->verifyUser->token;
          Mail::to($user->email)->send(new EmailVerification($user));
          $success['message'] = "Your Email is not verified, we have sent a confirmation mail to your email. Please check your inbox.";
        }else{
        $success['token'] = $user->createToken($user->name)->accessToken;
        $success['user'] = $user;
      }
        return response()->json(['success' => $success], 200);
      } else {
        return response()->json(['error' => 'Invalid Data'], 400);
      }
    }

    //User forgot password, send a token via mail
    public function ForgotPasswordRequest(Request $request)
    {
        //email
        $details = ['email' => $request->email];

        $validator = Validator::make($details, [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(["error" => $errors[0]], 400);
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
      $resetToken = PasswordReset::where('token', $token)->latest('created_at')->first();
      if(isset($resetToken) ){
        $request->session()->put(['email' => $resetToken->email]);
        $request->session()->put(['token' => $resetToken->token]);
        // $resetToken["current_date"] = date('Y-m-d H:i:s', strtotime(gmdate("Y-m-d H:i:s")));
        // $resetToken["expiry_date"] = date('Y-m-d H:i:s', strtotime($resetToken["created_at"]) + 86400);
        $expire_within_hours = ((strtotime($resetToken["created_at"]) + 86400) - strtotime(gmdate("Y-m-d H:i:s")) ) / 3600; 

        if($expire_within_hours < 0){
            return redirect('/message')->with('warning', "Your request has been expired.");
        }
        //if token is valid, redirect to reset form
        return view("passwordResetForm",compact('resetToken'));
      }
      return redirect('/message')->with('warning', "Sorry your email cannot be identified.");
    }

    //Submit new Password details by form
    public function resetPassword(Request $request){
      $newDetails = $request->all();
      $newDetails["email"] = session('email');
      $validator = Validator::make($newDetails, [
          'email' => 'required|email|exists:users,email',
          'password' => 'required|min:6',
          'password_confirmation' => 'required|same:password',
      ]);
      if ($validator->fails()) {
          $errors = $validator->errors()->all();
          return redirect()->back()->withErrors($validator)->withInput();
      }
      else{
        $user = User::where('email', $newDetails['email'])->first();
        $user['password'] = bcrypt($newDetails['password']);
        $user->save();
        Mail::to($user->email)->send(new PasswordResetSuccessful());
        return redirect('/message')->with('status', 'Your password has been reset successfully');
      }
    }

    //Password Reset by LoggedIn User
    public function resetPasswordByAuth(Request $request){
      $newDetails = $request->all();
      $user = Auth::user();
      $validator = Validator::make($newDetails, [
          'email' => 'required|email|exists:users,email',
          'password' => 'required|min:6',
          'c_password' => 'required|same:password',
      ]);
      if ($validator->fails()) {
          $errors = $validator->errors()->all();
          return response()->json(["error" => $errors[0]], 400);
      }
      else{
        $user['password'] = bcrypt($newDetails['password']);
        $user->save();
        Mail::to($user->email)->send(new PasswordResetSuccessful());
        return response()->json(["success" => ["message" => "Your password has been reset successfully."]], 200);
      }
    }
}
