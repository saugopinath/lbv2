<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgetpasswordRequest;
use App\Http\Requests\ValidateOtpRequest;
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
use Google2FA;
use App\Services\TwoFactor\TwoFactorAuthFactory;

class AuthenticationController  extends Controller
{
    public function __construct(

        protected AuthenticationService $authenticationService,
        protected SendSmsService $sendsmsService,
        protected UserService $userService,
    ) {}

    public function login()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.index');
        
    }

    public function loginCheck(LoginRequest $request)
    {
        $userData = $request->validated();

        $valid = 1;
        $userObj = $this->userService->findbyMobile($userData['mobile_no']);
        if (is_null($userObj)) {
            $valid = 0;
            return back()->withErrors(['mobile_no' => [__('messages.mobilenonotregister')]]);
        }
        $isPasswordSet = $this->userService->isPasswordSet($userObj->id);
        if ($isPasswordSet == false) {
            $valid = 0;
            return back()->withErrors(['password' => [__('messages.passwordnotsend')]]);
        }

        $validPassword = $this->userService->validPassword($userObj->id, $userData['password']);

        if ($validPassword == false) {
            $valid = 0;
            return back()->withErrors(['password' => [__('messages.invalidPassword')]]);
        }

        $isPasswordExpired = $this->userService->isPasswordExpired($userObj->id);
        // dd($isPasswordExpired);
        if ($isPasswordExpired == false) {
            $valid = 0;

            return back()->withErrors(['password' => [__('messages.passwordexpire')]]);
        }
        if ($valid == 1) {
            DB::beginTransaction();
            $generator = TwoFactorAuthFactory::getInstance()->create($userData['otp_totp_type']);
            $otp = $generator->generate();
            $displayCode = $generator->getDisplayCode($otp);
            $message = 'Your ' . $generator->getLabel() . ' for ANNAPURNA BHANDAR scheme login is ' . $displayCode . ' . ANNAPURNA BHANDAR, Govt of WB.';
            
            if ($generator->requiresSms()) {
                $snd_sms = $this->sendsmsService->sendSms($userData['mobile_no'], $message);
            } else {
                $snd_sms = true;
            }
            $smsTrack = $this->sendsmsService->SmstrackInsert($userObj->id, $userData['mobile_no'], $userData['otp_totp_type'],$displayCode, $message);
            $lastOtpStore = $this->authenticationService->userLastOtpStore($userObj->id, $displayCode, $userData['otp_totp_type']);
            if ($snd_sms && $smsTrack && $lastOtpStore) {
                DB::commit();
                $request->session()->put('otp_data', [
                    'mobile_no' => Crypt::encrypt($userData['mobile_no']),
                    'source_type' => Crypt::encrypt(2),
                    'user_id' => Crypt::encrypt($userObj->id),
                    'otp_totp_value' => Crypt::encrypt($otp),
                    'otp_totp_type' => Crypt::encrypt($userData['otp_totp_type'])
                ]);
               // dd('ok');
                return redirect()->route('otp-validate')->with('success', __('messages.' . $generator->getLabel() . 'send'));
            } else {
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

        if (empty($user_obj)) {
            // dd($user_obj);
            return back()->withErrors(['mobile_no' => [__('messages.mobilenonotregister')]]);
        }
        try {
            // dd($user_obj);
            DB::beginTransaction();
            // $otp = rand(111111,999999);
            $otp_totp_type = 12; // Default to OTP for forget password
            $generator = TwoFactorAuthFactory::getInstance()->create($otp_totp_type);
            $otp = $generator->generate();
            $displayCode = $generator->getDisplayCode($otp);
            $message = 'Your ' . $generator->getLabel() . ' for ANNAPURNA BHANDAR scheme login is ' . $displayCode . ' . ANNAPURNA BHANDAR, Govt of WB.';
            
            if ($generator->requiresSms()) {
                $snd_sms = $this->sendsmsService->sendSms($userData['mobile_no'], $message);
            } else {
                $snd_sms = true;
            }
            $smsTrack = $this->sendsmsService->SmstrackInsert($user_obj->id, $userData['mobile_no'], $otp_totp_type, $displayCode, $message);
            $lastOtpStore = $this->authenticationService->userLastOtpStore($user_obj->id, Crypt::encrypt($otp), $otp_totp_type);
            //dump($snd_sms);dump($smsTrack);dd($lastOtpStore);
            if ($snd_sms && $smsTrack && $lastOtpStore) {
                DB::commit();
                $request->session()->put('otp_data', [
                    'mobile_no' => Crypt::encrypt($userData['mobile_no']),
                    'source_type' => Crypt::encrypt(1),
                    'user_id' => Crypt::encrypt($user_obj->id),
                    'otp_totp_value' => Crypt::encrypt($otp),
                    'otp_totp_type' => Crypt::encrypt($userData['mobile_no']),
                ]);
               // dd($generator->getLabel());
                return redirect()->route('otp-validate')->with('success', __('messages.' . $generator->getLabel() . 'send'));
            } else {
                DB::rollBack();
                return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
            }
        } catch (\Exception $e) {
            //dd($e); 
            return back()->withErrors(['errors' => [__('messages.dbroolback')]]);
        }
    }
    public function otpVerification(Request $request)
    {
        try{
                    //dd('ok');
                    $otpSessionData = $request->session()->get('otp_data');
                    //dd($otpSessionData);
                    $mobile_no = Crypt::decrypt($otpSessionData['mobile_no']);
                    $user_id = Crypt::decrypt($otpSessionData['user_id']);
                    $source_type = Crypt::decrypt($otpSessionData['source_type']);
                    $otp_totp_type = Crypt::decrypt($otpSessionData['otp_totp_type']);
                    $qrCode = null;
                    if ($otp_totp_type == 13) {
                        $otp_totp_value = Crypt::decrypt($otpSessionData['otp_totp_value']);
                        $qrCode = Google2FA::getQRCodeInline(
                            config('app.name'),
                            $mobile_no,
                            $otp_totp_value
                        );
                    }

                    return view(
                        'auth.otpverification',
                        [
                            'mobile_no' => $mobile_no,
                            'user_id' => $user_id,
                            'source_type' => $source_type,
                            'otp_totp_type' => $otp_totp_type,
                            'qrCode' => $qrCode
                        ]
                    );
        }
        catch(\Exception $e){
            return redirect()->route('login')->withErrors(['errors' => [__('messages.something went wrong')]]);
        }
    }
    public function otpValidate(ValidateOtpRequest $request)
    {
         try{
        $otpDate = $request->validated();
        $otpSessionData = $request->session()->get('otp_data');
        $user_id = Crypt::decrypt($otpSessionData['user_id']);
        $source_type = Crypt::decrypt($otpSessionData['source_type']);
        $otp_totp_type = Crypt::decrypt($otpSessionData['otp_totp_type']);
        $otp_totp_value = Crypt::decrypt($otpSessionData['otp_totp_value']);

        $generator = TwoFactorAuthFactory::getInstance()->create($otp_totp_type);
        $isValid = $generator->validate($otp_totp_value, $otpDate['otp']);

        if (!$isValid) {
            return back()->withErrors(['otp' => [__('messages.invalid' . $generator->getLabel())]]);
        }

        if ($source_type == 1) {
            //dd('ok');
            return redirect('reset-password');
        }
        if ($source_type == 2) {
            $update_user = User::where('id', $user_id)
                ->update([
                    'flag_sent_otp' => 0
                ]);
            if ($update_user) {
                $user = User::where('id', $user_id)->where('is_active', 1)->first();
                // $address=$user->RoleSchemeOfficeMappings->Office;
                $request->session()->flush();
                Auth::login($user);
                return redirect('/dashboard');
            }
        }
        }
        catch(\Exception $e){
            return redirect()->route('login')->withErrors(['errors' => [__('messages.something went wrong')]]);
        }
    }
    public function resendOtp(Request $request)
    {
       
        try {
            $otpSessionData = $request->session()->get('otp_data');
            $mobile_no = Crypt::decrypt($otpSessionData['mobile_no']);
            $user_id = Crypt::decrypt($otpSessionData['user_id']);
            $source_type = Crypt::decrypt($otpSessionData['source_type']);
            $otp_totp_type = Crypt::decrypt($otpSessionData['otp_totp_type']);
            $otp_totp_value = Crypt::decrypt($otpSessionData['otp_totp_value']);
            $user_obj = $this->userService->find($user_id);
            DB::beginTransaction();
            $generator = TwoFactorAuthFactory::getInstance()->create($otp_totp_type);
            $otp = $generator->generate();
            $displayCode = $generator->getDisplayCode($otp);
            $message = 'Your ' . $generator->getLabel() . ' for ANNAPURNA BHANDAR scheme login is ' . $displayCode . ' . ANNAPURNA BHANDAR, Govt of WB.';
            
            if ($generator->requiresSms()) {
                $snd_sms = $this->sendsmsService->sendSms($mobile_no, $message);
            } else {
                $snd_sms = true;
            }
            $smsTrack = $this->sendsmsService->SmstrackInsert($user_obj->id, $mobile_no, $otp_totp_type,$displayCode, $message);
            $lastOtpStore = $this->authenticationService->userLastOtpStore($user_obj->id, $displayCode, $otp_totp_type);
            if ($snd_sms && $smsTrack && $lastOtpStore) {
               // dd('ok');
                DB::commit();
                session()->put('otp_data.otp_totp_value',Crypt::encrypt($otp));
                
               // dd('ok');
                return redirect()->route('otp-validate')->with('success', __('messages.' . $generator->getLabel() . 'send'));
            } else {
                DB::rollback();
                return back()->withErrors('errors', __('messages.dbroolback'));
            }
        }
        catch(\Exception $e){
            dd($e);
            return redirect()->route('login')->withErrors(['errors' => [__('messages.something went wrong')]]);
        }
    }
    public function resetPassword(Request $request)
    {
         try{
        $otpSessionData = $request->session()->get('otp_data');
        $user_id = Crypt::decrypt($otpSessionData['user_id']);
        $source_type = Crypt::decrypt($otpSessionData['source_type']);
        return view(
            'auth.resetpassword',
            [
                'user_id' => $user_id,
                'source_type' => $source_type
            ]
        );
        }
        catch(\Exception $e){
            return redirect()->route('login')->withErrors(['errors' => [__('messages.something went wrong')]]);
        }
    }
    public function resetPasswordPost(ResetPasswordPostRequest $request)
    {
        try{
        $otpDate = $request->validated();

        $otpSessionData = $request->session()->get('otp_data');
        $user_id = Crypt::decrypt($otpSessionData['user_id']);
        $source_type = Crypt::decrypt($otpSessionData['source_type']);
        $user_obj = $this->userService->find($user_id);




        if ($source_type == 1) {


            $c_time = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
            $password_expires_at = Carbon::now()->setTimezone('Asia/Kolkata')->addDays(90)->format('Y/m/d H:i:s');
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
        catch(\Exception $e){
            return redirect()->route('login')->withErrors(['errors' => [__('messages.something went wrong')]]);
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
