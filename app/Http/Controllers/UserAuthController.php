<?php

namespace App\Http\Controllers;

use App\User;
use App\PasswordReseting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\UserResource;
use App\Http\Resources\CoachLoginResource;
use App\Http\Resources\StudentLoginResoure;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Support\Facades\File;
use Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;

class UserAuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api')->except('registerUser', 'userLogin', 'forgot_password', 'token_connfrm', 'changePassword');
    }

    //----------------- [ Register user ] -------------------
    public function registerUser(UserRegisterRequest $request)
    {

        // check if email already registered
        $user  = User::where('email', $request->email)->first();
        if (!is_null($user)) {
            return response([
                'error' => true,
                'message' => 'Sorry! this email is already registered!',
            ], Response::HTTP_OK);
        } else {
            // create and return data
            $user = new User();
            $user->full_name =  $request->full_name;
            $user->student_id =   $request->student_id;
            $user->email =  $request->email;
            $user->profile_picture = $request->profile_picture;
            // 'username' =      $request->username,
            $user->phone_number =     $request->phone_number;
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return response([
                    'error' => false,
                    'message' => 'Success! you are logged in successfully',
                    'user' => new UserResource($user)
                ], Response::HTTP_OK);
            }else{
                return response([
                    'error' => true,
                    'message' => 'Something went wrong!',
                ], Response::HTTP_OK);
            }
            
            
        }
    }

    // -------------- [ User Login ] ------------------

    public function userLogin(UserLoginRequest $request)
    {
        $input              =       [
            'email'         =>          $request->email,
            'password'      =>          $request->password,
        ];

            if (Auth::attempt($input)) {

                // getting auth user after auth login
                $user = Auth::user();
                    return response([
                        'error' => false,
                        'message' => 'Success! you are logged in successfully',
                        'user' => new UserResource($user)
                    ], Response::HTTP_OK);
              
            } else {
                return response([
                    'error' => true,
                    'message' => 'Unauthorised, wrong credetials!',
                ], Response::HTTP_OK);
            }
 
    }

    // public function updateProfile(Request $request)
    // {
    //     $user = Auth::user();
    //     $user_id = $user->id;
    //     if ($user->user_type == "coach") {
    //         $coach =  coach::where('user_id', $user_id)->first();
    //         $coach->username =  $request->username;
    //         $coach->phone = $request->phone;
    //         $coach->DOB = $request->DOB;
    //         $coach->gender =  $request->gender;
    //         $coach->about = $request->about;
    //         $user->firstname = $request->firstname;
    //         $user->lastname = $request->lastname;
    //         $user->update();
    //         if ($request->password != null) {
    //             $user->password = Hash::make($request->password);
    //             $user->update();
    //         }
    //         if ($request->profile_pic_path != null) {
    //             $user->profile_pic_path = $request->profile_pic_path;
    //             if (!$this->validateString($request->profile_pic_path)) {
    //                 return response(["message" => "invalid base64 image string!"]);
    //             } else {
    //                 $coach->profile_pic_path = $this->moveUploadedFile($request->profile_pic_path, "CoachProfilePics");
    //             }
    //         }
    //         $coach->update();

    //         return response([
    //             'error' => false,
    //             'message' => 'Profile updated successfully',
    //             'user' => new UserResource($user)
    //         ], Response::HTTP_CREATED);
    //     } 
    // }

    // public function forgot_password(Request $request)
    // {
    //     $rules = [
    //         'email'    =>  'required|email',
    //     ];
    //     $error = Validator::make($request->all(), $rules);

    //     if ($error->fails()) {
    //         return response(['errors' => $error->errors()->all()], Response::HTTP_OK);
    //     } else {
    //         $emailexist = User::where('email', $request->email)->count();
    //         if ($emailexist <= 0) {
    //             return response([
    //                 'error' => true,
    //                 'message' => 'User not found',
    //             ], Response::HTTP_OK);
    //         } else {
    //             $digits = 4;
    //             $token = random_int(10 ** ($digits - 1), (10 ** $digits) - 1);

    //             $status = PasswordReseting::where('email', $request->email)->count();
    //             if ($status > 0) {
    //                 $pass =  PasswordReseting::where('email', $request->email)->first();
    //                 $pass->email = $request->email;
    //                 $pass->token = $token;
    //                 $data = [
    //                     'token'      =>  $token,
    //                 ];
    //                 if ($pass->update()) {
    //                     \Mail::to($request->email)->send(new SendMail($data));
    //                     return response([
    //                         'error' => false,
    //                         'message' => 'Password reset token sent',
    //                     ], Response::HTTP_OK);
    //                 } else {
    //                     return response([
    //                         'error' => true,
    //                         'message' => 'Failed to send token!',
    //                     ], Response::HTTP_OK);
    //                 }
    //             } else {

    //                 $pass = new PasswordReseting();
    //                 $pass->email = $request->email;
    //                 $pass->token = $token;
    //                 $data = [
    //                     'token'      =>  $token,
    //                 ];
    //                 if ($pass->save()) {
    //                     \Mail::to('noreply@myintercess.com')->send(new SendMail($data));
    //                     return response([
    //                         'error' => false,
    //                         'message' => 'Password reset token sent',
    //                     ], Response::HTTP_OK);
    //                 } else {
    //                     return response([
    //                         'error' => true,
    //                         'message' => 'Failed to send token!',
    //                     ], Response::HTTP_OK);
    //                 }
    //             }
    //         }
    //     }
    // }
    // public function token_connfrm(Request $request)
    // {
    //     $rules = [
    //         'email'    =>  'required|email',
    //         'token'    =>  'required',
    //     ];
    //     $error = Validator::make($request->all(), $rules);

    //     if ($error->fails()) {
    //         return response(['errors' => $error->errors()->all()], Response::HTTP_OK);
    //     } else {
    //         $status = PasswordReseting::where('email', $request->email)->where('token', $request->token)->count();
    //         if ($status > 0) {
    //             return response([
    //                 'error' => false,
    //                 'message' => 'Password reset token validated',
    //             ], Response::HTTP_OK);
    //         } else {
    //             return response([
    //                 'error' => true,
    //                 'message' => 'Password reset token invalid!',
    //             ], Response::HTTP_OK);
    //         }
    //     }
    // }
    // public function changePassword(Request $request)
    // {
    //     $rules = [
    //         'email'    =>  'required|email',
    //         'password'    =>  'required',
    //     ];
    //     $error = Validator::make($request->all(), $rules);

    //     if ($error->fails()) {
    //         return response(['errors' => $error->errors()->all()], Response::HTTP_OK);
    //     } else {
    //         $user = User::where('email', $request->email)->first();
    //         $user->password = Hash::make($request->password);
    //         if ($user->update()) {
    //             return response([
    //                 'error' => false,
    //                 'message' => 'Password updated successfuly!',
    //             ], Response::HTTP_OK);
    //         } else {
    //             return response([
    //                 'error' => true,
    //                 'message' => 'Password failed to update!',
    //             ], Response::HTTP_OK);
    //         }
    //     }
    // }


    public function moveUploadedFile($param, $folder)
    {
        $image = str_replace('data:image/png;base64,', '', $param);
        $image = str_replace(' ', '+', $image);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $imageName = sprintf('%s.%0.8s', $basename, "png");

        $filePath = $folder . "/" . $imageName;
        // return Storage::disk('local')->put($filePath, $uploadedFile_base64) ? $filePath : false;
        //check if the directory exists
        if (!File::isDirectory($folder)) {
            //make the directory because it doesn't exists
            File::makeDirectory($folder);
        }
        if (\File::put(public_path() . '/' . $filePath, base64_decode($image))) {
            return $imageName;
        } else {
            return null;
        }
    }

    //function to validate base64 string 

    public function validateString($s)
    {
        if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s) && base64_decode($s, true)) {
            return true;
        } else {
            return false;
        }
    }
}
