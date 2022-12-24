<?php

namespace App\Http\Controllers;
use App\Http\Requests\passwordResetRequest;
use App\Models\users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
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
 
    }
}
