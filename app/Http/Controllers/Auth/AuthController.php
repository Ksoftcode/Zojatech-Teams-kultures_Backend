<?php
// namespace App\mail; 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Http\Requests\forgetPasswordReques;
use App\Http\Requests\passwordResetRequest;
use App\Http\Requests\codeCheckRequest;
use App\Http\Requests\EmailVerificationRequest as RequestsEmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\users;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use App\Models\Crud;
use App\Models\File;
use App\Models\Music;
use Illuminate\Auth\Events\Registered;
use App\Traits\HttpResponses;
use App\Models\ResestCodePassword;
use App\Models\UserVerify;
use App\Notifications\EmailNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;
    use ResponseTrait;
    //  register
    public function register(request $request)
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
        $user->notify(new EmailNotification($user));

        // event(new Verified($user));
        $token = $user->createToken('authtoken');
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of')->plainTextToken,
        ], 'Register Successful please check your mail to verify your');
       
    }
         
             
               // login
              public function login(LoginUserRequest $request)
              {

                  $request->validated($request->only(['email', 'password']));
          
                  if (!Auth::attempt($request->only(['email', 'password']))) {
                      return $this->error('', 'Credentials do not match', 401);
                  }
          
                  $user = users::where('email', $request->email)->first();
          
                  return $this->success([
                      'user' => $user,
                      'access_token' => $user->createToken('API Token')->plainTextToken,
                  ], 'Login successful');
          
           
              }
          
       // forgetpassword
       public function forgetPassword(forgetPasswordReques $request)
       {
           
       
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
               if ($passwordReset->created_at > now()->addHour(2)) {
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
       // search code /filter
       
       public function search(Request $request)
       {
           $search=Music::query();
           if ($request->query('keyword')) {
               $search= $search->where('name','like','username','music','files','email','%'.$request->query('keyword'))->paginate('10');
           }
           if($request->query("afro_beat")=="afrobeat"){
               $search = $search->where("name",$request->query('afro'));
           }
           if ($request->query("world")=="world") {
               $search = $search->where("name", $request->query('world'));
           }
           if ($request->query("juju")=="juju") {
               $search = $search->where("name",$request->query('juju'));
           }
           $search=$search->get();
           return response()->json([
               'message'=>'search successful',
                   'data'=>[
                       "search"=>$search
                   ]
           ],200);
       // 
       }
    //    filter

    public function filter(Request $request)
    {
        try {
            //code...
            $filter = DB::table('')
                ->when($request->afro_beat, function ($q, $afro_beat) {
                    return $q->where('afro_beat', '=', $afro_beat);
                })
                ->when($request->world, function ($q, $world) {
                    return $q->where('world', '=', $world);
                })
                ->when($request->juju, function ($q, $juju) {
                    return $q->where('juju', '=', $juju);
                })
                ->orderByDesc('departments.id')
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return $this->badRequestResponse("Error", ['errror' => $th->getMessage()]);
        }
        return $this->successResponse('done', $filter);
    }
       // fileUpload Code
       
       public function fileupload(Request $request ){
           $file=new File();
       
                $file->files=$request->file('file')->store('apiFile');
                $result=$file->save();
                if ($result){
                    return["result"=>"file uploaded sucessfully"];
                }else{
                    return["result"=>"file upload failed"];
          }
        
            }        
           //  payStack Payment gateway code
           public function redirectToGateway(Request $request)
           {
               try{
                
                   $ref =  Paystack::genTranxRef();
                //    dd($request->all($ref));
                
                   $request['reference'] = $ref;
                   $request['amount'] = $request->amount * 100;
                   
                   return Paystack::getAuthorizationUrl($request->all())->redirectNow();
               }catch(\Exception $e) {
                   return Redirect::back()->
                                   withMessage(['msg'=>'The paystack token has expired.
                                    Please refresh the page and try again.', 'type'=>'error']);
               }        
           }
       
           /**
            * Obtain Paystack payment information
            * @return void
            */
           public function handleGatewayCallback()
           {
               $paymentDetails =  Paystack::getPaymentData();
             
                           return Paystack::getAuthorizationUrl($paymentDetails)->redirectNow();
       
    //    Crud Code

       }public function create(Request $request){
        $request->validate([
            'country'=>'required|string',
               'state'=>'required|string',
                'facebook'=>'required|string',
               'instagram'=> 'required|string',
              'linkdin'=> 'required|string',
            ]);    
            $user=new Crud([
           
             
                'country'=>$request->country,
                'state'=>$request->state,
                'facebook' =>$request->facebook,
                'instagram' =>$request->instagram,
                'linkdin'   =>$request->linkdin,
              
                
            ]);
          
            $user->save();
            return response()->json([
                'message'=>'created  successful',
                    'data'=>[
                        "user"=>$user
                    ]
            ],200);
                 }
    //    updates status for Crud

        public function update(Request $request, $id) 
           {
               try {
                   
                   $findCrud = Crud::findorfail($id);
                   $findCrud->country = $request->country ;
                   $findCrud->state = $request->state;
                   $findCrud->facebook = $request->facebook;
                   $findCrud->instagram = $request->insagram;
                   $findCrud->linkdin = $request->linkdin;
                   $findCrud->save();
               }
               
               catch (Exception $th) {
                   //throw $th;
                return response()->json(['message'=>'updated sucessfully'],200);
               }
       
               //   email verification code.
           } 
        
           public function verify(EmailVerificationRequest $request)
           {
               if ($request->users()->hasVerifiedEmail()) {
                   return [
                       'message' => 'Email already verified'
                   ];
               }
       
               if ($request->users()->markEmailAsVerified()) {
                   event(new Verified($request->user()));
               }
       
               return [
                   'message'=>'Email has been verified'
               ];
           }  
           
           public function veriyToken(Request $request)
           {
               if (empty($request->token)) {
                   return $this->badRequestResponse('Error', ['invalid token']);
               }
       
       
               $check = UserVerify::where('token', $request->token)->first();
               if (is_Null($check)) {
                   return $this->badRequestResponse('Error', ['invalid token']);
               }
       
       
       
       
               $user = Users::where('email', $check->user->email);
               if (is_null($check->user->email_verified_at)) {
                   $user->update([
                       'email_verified_at' => NOW(),
                   ]);
               }
               $token = $user->first()->createToken('myapp')->plainTextToken;
               return $this->successResponse('New user added', $token);
           }
}