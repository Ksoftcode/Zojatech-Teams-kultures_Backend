<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\users;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
}
