<?php
namespace App\Services;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Interfaces\AuthenticationInterface;
class AuthenticationService implements AuthenticationInterface
{
  
 
    public function credentialCheck(string $mobile_no, string $password): array
    {
        $current_timestamp = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
        $return_arr = array('is_valid' => false,'ddo_login' => 0, 'msg' => '','return_url' => '','user_id' => 0);
        $num = User::where('mobile_no', $mobile_no)->where('is_active', 1)->count('id');
        if($num>0){
            $user = User::where('mobile_no', $mobile_no)->where('is_active', 1)->first();
            if ($user->designation_id == 'DDO' || $user->designation_id == 'Corp' || $user->designation_id == 'Delegated DDO') {
                $return_arr['is_valid']=false; 
                $return_arr['ddo_login']=1; 
                $return_arr['msg']="For DDO users the site URL is : https://socialsecuritypayment.wb.gov.in/";
            }
            if(is_null($user->password) || $user->password==''){
                $return_arr['is_valid']=false; 
                $return_arr['return_url']='forget-password'; 
                $return_arr['msg']='Your password yet to set ..please set the passsword first';
            }
            if (!Hash::check($password, $user->password)) {
                $return_arr['is_valid']=false; 
                $return_arr['msg']='Please Provide the correct Password';
            }
            if (Hash::check($password, $user->password) && (strtotime($current_timestamp) > strtotime($user->password_expires_at))) {
                $return_arr['is_valid']=false; 
                $return_arr['msg']='Your password has expired ..please set the new passsword';
            }
            if ( Hash::check($password, $user->password) && (strtotime($current_timestamp) < strtotime($user->password_expires_at)) ){
                $return_arr['is_valid']=true; 
                $return_arr['user_id']= $user->id; 
            }

        }
       else{
        $return_arr['is_valid']=false;
        $return_arr['msg']='Your mobile number not match in our system..!!';
       
       }
       return $return_arr;
      
    }
    public function userLastOtpStore(int $userId, string $otp): bool
    {
        $cur_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
        $expire_time=Carbon::now()->setTimezone('Asia/Kolkata')->addMinutes(1)->format('Y/m/d H:i:s');
        $otp_hash=md5($otp);
        //dump($otp);dd($otp_hash);
        $update_user = User::where('id', $userId)
                        ->update([
                            'last_otp' =>  $otp_hash, 
                            'flag_sent_otp' => 1, 
                            'last_otp_generation_time' => $cur_time, 
                            'last_otp_expire_time' =>  $expire_time
                        ]);
       if($update_user)
       return true;
       else
       return false;
    }
    
    
}