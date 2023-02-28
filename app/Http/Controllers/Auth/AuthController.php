<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\codeCheckRequest;
use App\Http\Requests\forgetPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\passwordResetRequest;
use App\Mail\SendCodeResetPassword;
use App\Models\ResestCodePassword;
use App\Models\user;
use App\Models\users;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use HttpResponses;

    //  register
    public function register(Request $request)
    {
        $request->validate([
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'username' => 'required|string',
        'email' => 'required|string|unique:users',
        'password' => 'required|string|min:6',
        ]);
        $user = new users([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        // $token = rand(000, 999);

        // return response()->json(['message' => 'user has been registerd', $user, $token], 200);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of')->plainTextToken,
        ], 'Register Successful');
    }

    // login

    public function login(LoginUserRequest $request)
    {
        // $request->validate([
        //     'email' => ['required', 'exists:users,email'],
        //     'password' => 'required|string|min:6',
        // ]);
        // $users = users::where('email', $request->email)->first();
        // //  if(!$users || $users->email_verified_at=="") return "email not verified";

        // if (!$users) {
        //     return ' do not exsit';
        // }
        // if (!Hash::check($request->password, $users->password)) {
        //     throw ValidationException::withMessages(['message' => 'Wrong details']);
        // }

        $request->validated($request->only(['email', 'password']));

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = users::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'access_token' => $user->createToken('API Token')->plainTextToken,
        ], 'Login successful');

        // return response()->json(['message' => 'successful login'], 200);

        // $user = $request->user();
        // $tokenResult = $user->createtoken('personal access token')->plainTextToken;
        // $token = $tokenResult->token;
        // $token->expires_at = Carbon::now()->addweeks(1);

        // $token->save();

        // return response()->json(['data' => [
        //    'user' => Auth::user(),
        //      'access_token' => $tokenResult->accesstoken,
        //      'token_type' => 'Bearer',
        //      'expires_at' => Carbon::parse($tokenResult->token->expire_at)->toDateTimeString(),
        // ]]);
    }

    // forgetPassword
    public function forgetPassword(forgetPasswordRequest $request)
    {
        // $data = $request->validate([
        // 	'email' => 'required|email|exists:users',
        // ]);

        // Delete all old code that user send before.
        ResestCodePassword::where('email', $request->email)->delete();

        // Generate random code

        $data['email'] = $request->email;
        $data['Token'] = rand(000, 999);
        $data['created'] = now();

        // Create a new code
        $codeData = ResestCodePassword::create($data);

        // Send email to user

        Mail::to($request->email)->send(new SendCodeResetPassword($data['Token']));

        return response(['message' => trans('passwords.sent')], 200);

//    codeCheck
    }

public function codeCheck(codeCheckRequest $request)
{
    $request->validate([
        'Token' => 'required|string|exists:resetcodepasswords',
    ]);

    // find the code
    $passwordReset = ResestCodePassword::firstWhere('Token', $request->token);

    // check if it does not expired: the time is one hour
    if ($passwordReset->created_at > now()->addHour()) {
        $passwordReset->delete();

        return response(['message' => trans('passwords.code_is_expire')], 422);
    }

    return response([
        'Token' => $passwordReset->token,
        'message' => trans('passwords.code_is_valid'),
    ], 200);
}

    // passwordReset
    public function passwordReset(passwordResetRequest $request)
    {
        $user = users::where('email', $request->email)->first();

        if (!$user) {
            return 'This user is not found';
        }
        $user->fill([
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        DB::table('resetcodepassword')->
        where('email', $user->email)->
        delete();

        return response()->json([
          'message' => 'Password reset successful',
           'data' => [$user],
        ]);

        //  PasswordResetOpt
    }

 public function PasswordResetOpt(Request $request)
 {
     if (empty($request->token)) {
         return $this->badRequestResponse('Error');
     }
     $check = ResestCodePassword::where('token', $request->token)->first();
     if (is_null($check)) {
         return $this->badRequestResponse('Error invalid token');
     }

     return $this->successResponse('done', $check);
 }
}
