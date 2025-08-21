<x-layouts.guest>
    <!-- Session Status -->
    <p class="text-3xl font-semibold">OTP Validate</p>
    <x-errors class="mt-6"  />
    <x-success class="mt-6"  />
    <form class="mt-[60px] space-y-5" action="{{ route('otp-validate-post') }}" method="POST">
        @csrf
        <input type="hidden" name="token_id" value="{{Crypt::encrypt($user_id)}}">
        <input type="hidden" name="source_type" value="{{Crypt::encrypt($source_type)}}">
        <div class="relative">
            
            <x-text-input id="otp" class="form-input h-[66px] bg-transparent dark:bg-transparent text-base rounded-[10px] ps-5 pe-14" type="text" name="otp" :value="old('otp')" 
                autofocus autocomplete="off" placeholder="Enter OTP" maxlength="6"/>
                
            

        </div>
     
      
        <div class="flex mt-4">
            <div id="captcha-container">
                {!! captcha_img('math') !!}
            </div>
            <button type="button" onclick="refreshCaptcha()"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 26 26" width="26px" height="26px"><path d="M 13.8125 0 C 7.878906 0 4.082031 4.292969 4 10 L 0.5 10 C 0.300781 10 0.09375 10.113281 0.09375 10.3125 C -0.0078125 10.511719 -0.0078125 10.710938 0.09375 10.8125 L 6.09375 18.5 C 6.195313 18.601563 6.300781 18.6875 6.5 18.6875 C 6.699219 18.6875 6.804688 18.601563 6.90625 18.5 L 12.90625 10.8125 C 13.007813 10.710938 13.007813 10.511719 12.90625 10.3125 C 12.804688 10.113281 12.601563 10 12.5 10 L 9 10 C 9.066406 2.464844 12.921875 0.789063 13.8125 0.09375 C 14.011719 -0.0078125 14.011719 0 13.8125 0 Z M 19.5 7.34375 C 19.351563 7.34375 19.195313 7.398438 19.09375 7.5 L 13.09375 15.1875 C 12.992188 15.386719 13 15.585938 13 15.6875 C 13.101563 15.886719 13.304688 16 13.40625 16 L 17 16 C 16.933594 23.535156 13.078125 25.210938 12.1875 25.90625 C 11.988281 26.007813 11.988281 26 12.1875 26 C 18.121094 26 21.917969 21.707031 22 16 L 25.40625 16 C 25.605469 16 25.8125 15.886719 25.8125 15.6875 C 26.011719 15.488281 26.007813 15.289063 25.90625 15.1875 L 19.90625 7.5 C 19.804688 7.398438 19.648438 7.34375 19.5 7.34375 Z"/></svg></button>
        </div>
        <div class="relative ">
            <x-text-input id="captcha" class="form-input h-[66px] bg-transparent dark:bg-transparent text-base rounded-[10px] ps-5 pe-14 " type="text" name="captcha" :value="old('captcha')" 
            autofocus autocomplete="off" placeholder="Captcha"/>
           
        </div>
        <div class="text-center mt-[30px]">
            <a href="javascript:;" onclick="resendOtp()">Resend code</a>
          
        </div>
        <a href="{{route('login')}}" class="rounded-lg capitalize flex items-center gap-2.5 text-success">
            <svg width="24" height="24" class="inline-block" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M20.75 12C20.75 11.5858 20.4142 11.25 20 11.25H10.75V12.75H20C20.4142 12.75 20.75 12.4142 20.75 12Z" fill="currentColor" />
                <path d="M10.75 18C10.75 18.3034 10.5673 18.5768 10.287 18.6929C10.0068 18.809 9.68417 18.7449 9.46967 18.5304L3.46967 12.5304C3.32902 12.3897 3.25 12.1989 3.25 12C3.25 11.8011 3.32902 11.6103 3.46967 11.4697L9.46967 5.46969C9.68417 5.25519 10.0068 5.19103 10.287 5.30711C10.5673 5.4232 10.75 5.69668 10.75 6.00002V18Z" fill="currentColor" />
            </svg>
            Back To Login
        </a>
        <div class="!mt-[50px]">
            <button type="submit" class="" value="submit1">
                Validate OTP
            </button>
        </div>
    </form>
    <form  action="{{ route('resendOtp') }}" method="POST" id="resendForm">
        @csrf
        <input type="hidden" name="token_id" value="{{Crypt::encrypt($user_id)}}">
        <input type="hidden" name="source_type" value="{{Crypt::encrypt($source_type)}}">
    </form>
    <script>
        function refreshCaptcha() {
            fetch("{{ route('refresh-captcha') }}")
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captcha-container').innerHTML = data;
                });
        }
        function encryptPasswordsforLoginForm() {
            encryptPasswords(
                document.getElementById('password'),
            );
        }
        function resendOtp() {
            document.getElementById("resendForm").submit();
        }
    </script>
</x-layouts.guest>