<x-layouts.guest>
    <!-- Session Status -->
    <x-errors class="mt-4"/>
    <form class="mt-4 space-y-5" action="{{ route('forgetpasswordPost') }}" method="POST">
        @csrf
         <div>
            <x-publicForm.text-input
            id="mobile" 
                 class="w-full h-12 px-5 border border-gray-300 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="mobile_no" :value="old('mobile_no')" 
                autofocus autocomplete="off" placeholder="Mobile Number" maxlength="10"/>
        </div>
     
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <x-publicForm.text-input id="captcha" class="w-full sm:w-[55%] h-12 px-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="captcha" :value="old('captcha')" 
            autofocus autocomplete="off" placeholder="Captcha"/>
           <x-publicForm.captcha />
        </div>
                <div class="mt-4 space-y-5">
            <x-publicForm.button type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200">
                Send OTP
            </x-publicForm.button>


        <div class="flex justify-center items-center gap-2 mt-2">
        <a href="{{route('login')}}" class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium">
            <svg width="24" height="24" class="inline-block" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M20.75 12C20.75 11.5858 20.4142 11.25 20 11.25H10.75V12.75H20C20.4142 12.75 20.75 12.4142 20.75 12Z" fill="currentColor" />
                <path d="M10.75 18C10.75 18.3034 10.5673 18.5768 10.287 18.6929C10.0068 18.809 9.68417 18.7449 9.46967 18.5304L3.46967 12.5304C3.32902 12.3897 3.25 12.1989 3.25 12C3.25 11.8011 3.32902 11.6103 3.46967 11.4697L9.46967 5.46969C9.68417 5.25519 10.0068 5.19103 10.287 5.30711C10.5673 5.4232 10.75 5.69668 10.75 6.00002V18Z" fill="currentColor" />
            </svg>
            Back To Login
        </a>
        </div>
        <!-- <div class="!mt-[50px]">
            <button type="submit" class="">
                Send OTP
            </button>
        </div> -->
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
    </script>
</x-layouts.guest>