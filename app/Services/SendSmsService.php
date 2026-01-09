<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Models\VerificationCode;
use App\Interfaces\SendSmsInterface;

class SendSmsService implements SendSmsInterface
{
  
  
    public function sendsms(string $mobile_no, string $msg): bool
    {
          if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging'){
              return true;
           }
        else
         {
             try{
            Http::withUrlParameters([
            'endpoint' => 'https://bulkpush.mytoday.com/BulkSms/SingleMsgApi',
            'feedid' => 379523,
            'username' => '8017072222',
            'password' => 'newAuth\$gL22m',
            'senderid' => 'WB_JAIBANGLAOTP',
            'To' => $mobile_no,
            'Text' => urlencode($msg),
        ])->get('{+endpoint}/{feedid}/{username}/{password}/{senderid}/{To}/{Text}');
                return true;
            }
            catch(\Exception $e){
                return false;
                
            }
           
         }
    }
    public function SmstrackInsert(int $user_id, string $mobile_no,string $otp): bool
    {
        $is_inserted=VerificationCode::create([
            'user_id' => $user_id,
            'otp' => md5($otp),
            'mobile_no' => $mobile_no, 
       ]);
       if( $is_inserted){
        return true; 
       } 
       else{
        return false; 
       }
    }
}