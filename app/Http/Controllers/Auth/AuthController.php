<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Http\Requests\forgetPasswordReques;
use App\Http\Requests\passwordResetRequest;
use App\Http\Requests\codeCheckRequest;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\users;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use App\Models\ResestCodePassword;

use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register 
    public function register(Request $request){
$request->validate([
'firstname'=>'required|string',
'lastname'=>'required|string',
'username'=>'required|string',
'email'=> 'required|string|unique:users',
'password'=>'required|string|min:6',

]);
$user=new users([
    'firstname'=>$request->firstname,
    'lastname'=>$request->lastname,
    'email'    =>$request->email,
    'username' =>$request->username,
    'password'=> Hash::make($request->password)

]);
$user-> save();
$token=rand(000,99999);

return response()->json(['message'=>'user has been registerd',$user,$token],200);

    }
    // login
    public function login(Request $request){
        $request->validate([
            'email'=>['required','exists:users,email'],
            'password'=>'required|string|min:6'
        ]);
       $users = users::where('email',$request->email)->first();
        //  if(!$users || $users->email_verified_at=="") return "email not verified"; 
    
         if(!$users) return " do not exsit";
    if (!Hash::check($request->password,$users->password)) {
      throw ValidationException::withMessages(["message"=>"Wrong details"]);
      
    }
    return response()->json(['message'=>'successful login'],200);
    
    
    $user= $request->user();
    $tokenResult= $user->createtoken('personal access token');
    $token=$tokenResult->token;
    $token->expires_at=Carbon::now()->addweeks(1);
    
    $token->save();
    return response()->json(['data'=>[
       'user'=>Auth::user(),
         'access_token'=>$tokenResult->accesstoken,
         'token_type'=>'Bearer',
         'expires_at'=>Carbon::parse($tokenResult->token->expire_at)->toDateTimeString()
         
    ]]);
   
}
// forgetpassword
public function forgetPassword(forgetPasswordReques $request)
{
    // $data = $request->validate([
    // 	'email' => 'required|email|exists:users',
    // ]);

    // Delete all old code that user send before.
    ResestCodePassword::where('email', $request->email)->delete();

    // Generate random code
    
    $data['email'] = $request->email;
    $data['Token'] =  rand(000,999);
    $data['created'] = now();
   
    // Create a new code
    $codeData =ResestCodePassword::create($data);

    // Send email to user
    
    Mail::to($request->email)->send(new SendCodeResetPassword($data['Token'])); 
    
    return response(['message' => trans('passwords.sent')], 200);

}
// code check
public function codeCheck(codeCheckRequest $request)
    {
        $request->validate([
            'Token' => 'required|string|exists:resetcodepasswords',
        ]);

        // find the code
        $passwordReset =ResestCodePassword::firstWhere('Token', $request->token);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        return response([
            'Token' => $passwordReset->token,
            'message' => trans('passwords.code_is_valid')
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
            'password' => Hash::make($request->password)
        ]);
        $user->save();
        DB::table('resetcodepassword')->
        where('email',$user->email)->
        delete();        

        return response()->json([
          "message"=> "Password reset successful",
           "data"=>[$user]
        ]);
 
//  PasswordResetOpt
   
} public function PasswordResetOpt(Request $request)
    {
        if (empty($request->token)) return $this->badRequestResponse("Error");
        $check = ResestCodePassword::where('token', $request->token)->first();
        if (is_Null($check)) return $this->badRequestResponse('Error invalid token');
        return $this->successResponse("done", $check);
    }
    // logout
public function logout(Request $request){
    
    Auth::logout();
   return response()->json(['message' => 'Successfully logged out']);

}

}