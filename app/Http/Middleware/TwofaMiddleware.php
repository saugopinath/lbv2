<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Services\UserService;
class TwofaMiddleware
{
      public function __construct(
        protected UserService $userService
    ) {
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       try{
        $user_id = Crypt::decrypt($request->get('token_id'));
        $source_type = Crypt::decrypt($request->get('source_type'));
        $user_obj = $this->userService->find($user_id);
        if(is_null($user_obj)){
            return redirect('login')->withErrors(['errors' => [__('messages.invalidSignature')]]);
        }
        if(strtotime(Carbon::now()->format('Y/m/d H:i:s')) > strtotime($user_obj->last_otp_expire_time)){
            return redirect('login')->withErrors(['errors' => [__('messages.otpexpired')]]);
        }
        if($source_type==1){
            $flag_sent_otp = $user_obj->flag_sent_otp;
            if(is_null($flag_sent_otp) || $flag_sent_otp==''){
                return redirect('login')->withErrors(['errors' => [__('messages.invalidSignature')]]);
            }

        }
        return $next($request);
    }
   catch (\Exception $e) {  
           // dd($e); 
           return redirect('login')->withErrors(['errors' => [__('messages.invalidSignature')]]);
                
            }
    }
}
