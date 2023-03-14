<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\codeCheckRequest;
use App\Http\Requests\forgetPasswordReques;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\passwordResetRequest;
use App\Http\Traits\ResponseTrait;
use App\Mail\SendCodeResetPassword;
use App\Models\Crud;
use App\Models\File;
use App\Models\Music;
use App\Models\ResestCodePassword;
use App\Models\user;
use App\Models\users;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Facades\Paystack;

class AuthController extends Controller
{
    // Register

    use ResponseTrait;

    //  register
    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|unique:users',
            'country' => 'required|string',
            'state' => 'required|string',
            'facebook' => 'required|string',
            'instagram' => 'required|string',
            'linkdin' => 'required|string',
            'password' => 'required|string|min:6',
            ]);

        $user = new users([
             'firstname' => $request->firstname,
             'lastname' => $request->lastname,
             'email' => $request->email,
             'country' => $request->country,
             'state' => $request->state,
             'facebook' => $request->facebook,
             'instagram' => $request->instagram,
             'linkdin' => $request->linkdin,
             'username' => $request->username,
             'password' => Hash::make($request->password),
         ]);
        $user->save();
        // $token = rand(000, 999);

        // return response()->json(['message' => 'user has been registerd', $user, $token], 200);

        return $this->successResponse('Register Successful', [
            'user' => $user,
            'token' => $user->createToken('API Token of')->plainTextToken,
        ]);
    }

    // Login

          public function login(LoginUserRequest $request)
          {
              $request->validated($request->only(['email', 'password']));

              if (!Auth::attempt($request->only(['email', 'password']))) {
                  return $this->unauthenticatedResponse('Credentials do not match');
              }

              $user = users::where('email', $request->email)->first();

              return $this->successResponse('Login successful', [
                  'user' => $user,
                  'access_token' => $user->createToken('API Token')->plainTextToken,
              ]);
          }

      // forgetpassword
      public function forgetPassword(forgetPasswordReques $request)
      {
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
      }

      // code check
      public function codeCheck(codeCheckRequest $request)
      {
          $request->validate([
              'Token' => 'required|string|exists:resetcodepasswords',
          ]);

          // find the code
          $passwordReset = ResestCodePassword::firstWhere('Token', $request->token);

          // check if it does not expired: the time is one hour
          if ($passwordReset->created_at > now()->addHour(2)) {
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

          // logout
      public function logout(Request $request)
      {
          Auth::logout();

          return response()->json(['message' => 'Successfully logged out']);
      }
      // search code /filter

      public function search(Request $request)
      {
          $search = Music::query();
          if ($request->query('keyword')) {
              $search = $search->where('name', 'like', 'userame', 'music', 'files', 'email', '%'.$request->query('keyword'))->paginate('10');
          }
          if ($request->query('afrobeat') == 'afrobeat') {
              $search = $search->where('name', $request->query('afro'));
          }
          if ($request->query('world') == 'world') {
              $search = $search->where('name', $request->query('world'));
          }
          if ($request->query('juju') == 'juju') {
              $search = $search->where('name', $request->query('juju'));
          }
          $search = $search->get();

          return response()->json([
              'message' => 'search successful',
                  'data' => [
                      'search' => $search,
                  ],
          ], 200);
      }
      // fileUpload Code

      public function fileupload(Request $request)
      {
          $file = new File();

          $file->files = $request->file('file')->store('apiFile');
          $result = $file->save();
          if ($result) {
              return ['result' => 'file uploaded sucessfully'];
          } else {
              return ['result' => 'file upload failed'];
          }
      }

          //  payStack Payment gateway code
          public function redirectToGateway(Request $request)
          {
              try {
                  $ref = Paystack::genTranxRef();
                  // dd($request->$ref);

                  $request['reference'] = $ref;
                  $request['amount'] = $request->amount * 100;

                  return Paystack::getAuthorizationUrl($request->all())->redirectNow();
              } catch (\Exception $e) {
                  return Redirect::back()->
                                  withMessage(['msg' => 'The paystack token has expired.
                                    Please refresh the page and try again.', 'type' => 'error']);
              }
          }

          /**
           * Obtain Paystack payment information.
           *
           * @return void
           */
          public function handleGatewayCallback()
          {
              $paymentDetails = Paystack::getPaymentData();

              return Paystack::getAuthorizationUrl($paymentDetails)->redirectNow();
          }

       public function update(Request $request, $id)
       {
           try {
               $findCrud = Crud::findorfail($id);
               $findCrud->country = $request->country;
               $findCrud->state = $request->state;
               $findCrud->facebook = $request->facebook;
               $findCrud->instagram = $request->insagram;
               $findCrud->linkdin = $request->linkdin;
               $findCrud->save();
           } catch (\Exception $th) {
               // throw $th;
               return response()->json(['message' => 'updated sucessfully'], 200);
           }
        //   email verification code.

        // return response()->json(['message' => 'user has been registerd', $user, $token], 200);
       }

 public function sendVerificationEmail(Request $request)
 {
     if ($request->users()->hasVerifiedEmail()) {
         return [
             'message' => 'Already Verified',
         ];
     }

     $request->users()->sendEmailVerificationNotification();

     return ['status' => 'verification-link-sent'];
 }

          public function verify(EmailVerificationRequest $request)
          {
              if ($request->users()->hasVerifiedEmail()) {
                  return [
                      'message' => 'Email already verified',
                  ];
              }

              if ($request->users()->markEmailAsVerified()) {
                  event(new Verified($request->user()));
              }

              return [
                  'message' => 'Email has been verified',
              ];
          }
}
