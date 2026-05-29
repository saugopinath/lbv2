<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Models\VerificationCode;
use App\Interfaces\SendSmsInterface;

class SendSmsService implements SendSmsInterface
{
  
  
    public function sendSms(string $mobile_no, string $msg): bool
    {
        $sendOnLocalStaging = config('services.ifms.send_on_local_staging', false);
        $appEnv = env('APP_ENV', 'production');

        if (($appEnv === 'local' || $appEnv === 'staging') && !$sendOnLocalStaging) {
            return true;
        }

        /*
        // LEGACY BULKPUSH SMS SERVICE CODE (Commented out but preserved)
        try {
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
        } catch (\Exception $e) {
            return false;
        }
        */

        // NEW IFMS REST API IMPLEMENTATION
        try {
            $otp = null;
            if (preg_match('/login is (\d+)/i', $msg, $matches)) {
                $otp = $matches[1];
            }

            $baseUrl = config('services.ifms.sms_base_url', 'https://train-ifms.wb.gov.in');
            $templateId = config('services.ifms.sms_template_id');

            if (empty($templateId)) {
                \Illuminate\Support\Facades\Log::error('IFMS SMS template ID is not configured.');
                return false;
            }

            $url = rtrim($baseUrl, '/') . '/api/Sms/SendSms';

            $payload = [
                'phoneNumber' => $mobile_no,
                'templateId'  => $templateId,
                'otp'         => $otp,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($url, $payload);

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('IFMS SMS API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'url'    => $url,
                ]);
                return false;
            }

            $data = $response->json();
            $status = $data['apiResponseStatus'] ?? null;

            if ($status === 1) {
                return true;
            }

            \Illuminate\Support\Facades\Log::error('IFMS SMS API returned non-success response.', [
                'response' => $data,
                'payload'  => $payload,
            ]);
            return false;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('IFMS SMS service exception occurred.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
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