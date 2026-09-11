<x-layouts.guest>
    <x-errors class="mt-6" />
    <x-success class="mt-6" />

    <form class="mt-4 space-y-4" action="{{ route('loginPost') }}" method="POST">
        
        
        @csrf
        <div>
            <x-publicForm.text-input
                id="mobile"
                name="mobile_no"
                placeholder="Mobile Number"
                maxlength="12"
                autofocus autocomplete="off"
                :value="old('mobile_no')"
                class="w-full px-5 h-12 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                required />
        </div>
        <div>
            <x-publicForm.password-input
                id="password"
                name="password"
                placeholder="Password"
                autofocus autocomplete="off"
                class="w-full px-5 h-12 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg" />
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2">
            <x-publicForm.text-input
                id="captcha"
                name="captcha"
                placeholder="Captcha"
                :value="old('captcha')"
                class="w-full sm:w-[55%] h-12 px-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
            <x-publicForm.captcha />
        </div>
        <!-- OTP / TOTP -->
    

                {{-- <x-form.radio id="check_aadhaar" name="aadhaar_number" label="OTP" value="12"
                
                />
                 <x-form.radio id="check_aadhaar" name="aadhaar_number" label="TOTP" value="13"
                
                /> --}}
                <div class="flex flex-col sm:flex-row items-center gap-2">
                <x-form.radio id="check_otp" name="verification_type" label="OTP" value="12" />
                <x-form.radio id="check_totp" name="verification_type" label="TOTP" value="13" />
                </div>
        
<x-publicForm.button id="loginButton" type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 !text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200 flex justify-center items-center gap-3">
    Send OTP
</x-publicForm.button>
<script>
    document.querySelectorAll('input[name="verification_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.getElementById('loginButton').textContent =
                this.value === '13' ? 'Send TOTP' : 'Send OTP';
        });
    });
</script>  
    </form>
    <div class="text-right mt-2 text-blue-600 italic text-sm hover:underline cursor-pointer">
        <a href="{{ route('forget-password') }}">Forgot Password?</a>
    </div>
</x-layouts.guest>