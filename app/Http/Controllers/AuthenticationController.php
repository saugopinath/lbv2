<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgetpasswordRequest;
use App\Http\Requests\OtpVerificationRequest;
use App\Http\Requests\ValidateOtpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetPasswordPostRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Services\AuthenticationService;
use App\Services\SendSmsService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User_audit_trail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
class AuthenticationController  extends Controller
{
    public function __construct(
        
        protected AuthenticationService $authenticationService,
        protected SendSmsService $sendsmsService,
        protected UserService $userService,
    ) {
    }

    public function login()
    {
        if(auth()->check()){
            return redirect()->route('dashboard');
        }

        return view('auth.index');
    }

    public function loginCheck(LoginRequest $request)
    {
        $userData = $request->validated(); 
        
        $valid=1;
        $userObj= $this->userService->findbyMobile($userData['mobile_no']);
        if(is_null($userObj)){
            $valid=0;
            return back()->withErrors(['mobile_no' => [__('messages.mobilenonotregister')]]);
        }
        $isPasswordSet= $this->userService->isPasswordSet($userObj->id);
        if($isPasswordSet==false){
            $valid=0;
            return back()->withErrors(['password' => [__('messages.passwordnotsend')]]);
        }
      
        $validPassword= $this->userService->validPassword($userObj->id,$userData['password']);
       
        if($validPassword==false){
            $valid=0;
            return back()->withErrors(['password' => [__('messages.invalidPassword')]]);
        }
       
        $isPasswordExpired= $this->userService->isPasswordExpired($userObj->id);
       // dd($isPasswordExpired);
        if($isPasswordExpired==false){
            $valid=0;
           
            return back()->withErrors(['password' => [__('messages.passwordexpire')]]);
        }
        if($valid==1){
            DB::beginTransaction();
            if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
              $otp='123456';
           else
            $otp = rand(111111,999999);
            $message = 'Your OTP for Lakshmir Bhandar scheme login is ' . $otp . ' . Lakshmir Bhandar, Govt of WB.';
            $snd_sms = $this->sendsmsService->sendSms($userData['mobile_no'], $message);
            $smsTrack =$this->sendsmsService->SmstrackInsert($userObj->id,$userData['mobile_no'],$otp, $message);
            $lastOtpStore =$this->authenticationService->userLastOtpStore($userObj->id,$otp);
            if($snd_sms && $smsTrack && $lastOtpStore){
                DB::commit();
                return redirect()->route('otp-validate',['source_type'=>Crypt::encrypt(2), 'token_id'=>Crypt::encrypt($userObj->id)])->with('success', __('messages.otpsend'));
            }else{
                DB::rollback();
                return back()->withErrors('errors', __('messages.dbroolback'));
            }
           
        }
       
    } 
    public function forgetPassword(): \Illuminate\View\View
    {
        return view('auth.forgetpassword');
    }
    public function forgetPasswordPost(ForgetpasswordRequest $request): \Illuminate\Http\RedirectResponse
    {
        $userData = $request->validated(); 
        $user_obj = $this->userService->findbyMobile($userData['mobile_no']);
        //dd($userData['mobile_no']);
       
        if (empty($user_obj)){
           // dd($user_obj);
           return back()->withErrors(['mobile_no' => [__('messages.mobilenonotregister')]]);
        }
        try{
           // dd($user_obj);
        DB::beginTransaction();
        // $otp = rand(111111,999999);
       if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
              $otp='123456';
        else
        $otp = rand(111111,999999);
         $message = 'Your OTP for Lakshmir Bhandar scheme login is ' . $otp . ' . Lakshmir Bhandar, Govt of WB.';
         $snd_sms = $this->sendsmsService->sendSms($userData['mobile_no'], $message);
         $smsTrack =$this->sendsmsService->SmstrackInsert($user_obj->id,$userData['mobile_no'],$otp, $message);
         $lastOtpStore =$this->authenticationService->userLastOtpStore($user_obj->id,$otp);
         //dump($snd_sms);dump($smsTrack);dd($lastOtpStore);
         if($snd_sms && $smsTrack && $lastOtpStore){
             DB::commit();
             return redirect()->route('otp-validate',['source_type'=>Crypt::encrypt(1), 'token_id'=>Crypt::encrypt($user_obj->id)])->with('success', __('messages.otpsend'));
         }else{
             DB::rollBack();
             return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
         }
        }   catch (\Exception $e) {  
            //dd($e); 
            return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
                
            }
        
    } 
    public function otpVerification(OtpVerificationRequest $request)
    {
        $otpData=$request->validated();
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
        return view('auth.otpverification', [
            'user_id' => $user_id,
            'source_type' => $source_type
            ]
             );
       
       
    }
    public function otpValidate(ValidateOtpRequest $request)
    {
        $otpDate = $request->validated(); 
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
      
        if($source_type == 1){
            //dd('ok');
            return redirect('reset-password?token_id='.Crypt::encrypt($user_id).'&source_type='.Crypt::encrypt($source_type));
       }
    if($source_type == 2){
            $update_user = User::where('id', $user_id)
                        ->update([
                            'flag_sent_otp' => 0
                        ]);
                    if($update_user){
                          $user = User::where('id', $user_id)->where('is_active', 1)->first(); 
                         // $address=$user->RoleSchemeOfficeMappings->Office;
                          $request->session()->flush();
                          Auth::login($user);                
                          return redirect('/dashboard');
                        }

    }
    }
    public function resendOtp(Request $request)
    {
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
        $user_obj = $this->userService->find($user_id);
        
        try{
            // dd($user_obj);
         DB::beginTransaction();
         // $otp = rand(111111,999999);
          if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging')
            $otp='123456';
         else
            $otp = rand(111111,999999);
          $message = 'Your OTP for Lakshmir Bhandar scheme login is ' . $otp . ' . Lakshmir Bhandar, Govt of WB.';
          $snd_sms = $this->sendsmsService->sendSms($user_obj->mobile_no, $message);
          $smsTrack =$this->sendsmsService->SmstrackInsert($user_obj->id,$user_obj->mobile_no,$otp, $message);
          $lastOtpStore =$this->authenticationService->userLastOtpStore($user_obj->id,$otp);
          //dump($snd_sms);dump($smsTrack);dd($lastOtpStore);
          if($snd_sms && $smsTrack && $lastOtpStore){
              DB::commit();
              return redirect()->route('otp-validate',['source_type'=>Crypt::encrypt($source_type), 'token_id'=>Crypt::encrypt($user_obj->id)])->with('success', __('messages.otpsend'));
          }else{
              DB::rollBack();
              return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
          }
         }   catch (\Exception $e) {  
            // dd($e); 
             return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
                 
             }
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $otpData=$request->validated();
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
        return view('auth.resetpassword', [
            'user_id' => $user_id,
            'source_type' => $source_type
            ]
             );
       
       
    }
    public function resetPasswordPost(ResetPasswordPostRequest $request)
    {
       // dd('ok');
        $otpDate = $request->validated(); 
       
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
        $user_obj = $this->userService->find($user_id);
        
     
      
   
        if($source_type == 1){
            
           
            $c_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
            $password_expires_at= Carbon::now()->setTimezone('Asia/Kolkata')->addDays(90)->format('Y/m/d H:i:s');
            DB::beginTransaction();
           
            $inserttrail = array(
                'old_password' => $user_obj->password,
                'new_password' => bcrypt($request->user_password),
                'operation_type' => 10,
                'operate_by' => $user_obj->id,
                'operate_to_user_id' => $user_obj->id,
                'ip_address' => request()->ip(),
                'user_agent' => $request->header('User-Agent'),
                'operation_time' => $c_time
            );
            $trailSave = User_audit_trail::create($inserttrail);
            $trail_id = $trailSave->id;
          
            $update_user = User::where('id', $user_obj->id)->update([
                    'password' => bcrypt($request->user_password), 
                    'flag_sent_otp' => 0, 
                    'password_set_time' => $c_time, 
                    'password_expires_at' => $password_expires_at, 
                    'updated_at' => $c_time
                ]);
                
                if ($update_user && $trail_id) {
                    DB::commit();
                    return redirect('login')->with('success', __('messages.passwordsucessfullyreset'));
                } else {
                    DB::rollback();
                    return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
                }
       }
   
    }
     public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

}