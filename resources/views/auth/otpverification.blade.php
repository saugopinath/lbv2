<x-layouts.guest>
    <!-- Error & Success Messages -->
    <x-errors class="mt-4" />
    <x-success class="mt-4" />

    <form action="{{ route('otp-validate-post') }}" method="POST" class="mt-4 space-y-4">
        @csrf
        <input type="hidden" name="token_id" value="{{ Crypt::encrypt($user_id) }}">
        <input type="hidden" name="source_type" value="{{ Crypt::encrypt($source_type) }}">
        <!-- OTP Input -->
        <div>
            <x-publicForm.text-input
                id="otp"
                name="otp"
                type="text"
                maxlength="6"
                placeholder="Enter OTP"
                autofocus
                autocomplete="off"
                :value="old('otp')"
                class="w-full h-12 px-5 border border-gray-300 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <!-- Captcha -->
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <x-publicForm.text-input
                id="captcha"
                name="captcha"
                placeholder="Captcha"
                :value="old('captcha')"
                class="w-full sm:w-[55%] h-12 px-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />
            <x-publicForm.captcha />
        </div>

        <div class="mt-4 space-y-5">
            <x-publicForm.button type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200">
                Validate OTP
            </x-publicForm.button>

            <x-publicForm.button onclick="resendOtp()" class="w-full h-12 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200">
                Resend Code
            </x-publicForm.button>
        </div>

        <!-- Resend Link -->
        <!-- <div class="text-center mt-4">
            <button type="button" onclick="resendOtp()" class="text-blue-600 hover:underline text-sm font-medium">
                Resend Code
            </button>
        </div> -->

        <!-- Back to Login -->
        <div class="flex justify-center items-center gap-2 mt-2">
            <a href="{{ route('login') }}" class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium">
                <svg width="20" height="20" class="inline-block" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M20.75 12C20.75 11.5858 20.4142 11.25 20 11.25H10.75V12.75H20C20.4142 12.75 20.75 12.4142 20.75 12Z" fill="currentColor" />
                    <path d="M10.75 18C10.75 18.3034 10.5673 18.5768 10.287 18.6929C10.0068 18.809 9.68417 18.7449 9.46967 18.5304L3.46967 12.5304C3.32902 12.3897 3.25 12.1989 3.25 12C3.25 11.8011 3.32902 11.6103 3.46967 11.4697L9.46967 5.46969C9.68417 5.25519 10.0068 5.19103 10.287 5.30711C10.5673 5.4232 10.75 5.69668 10.75 6.00002V18Z" fill="currentColor" />
                </svg>
                Back to Login
            </a>
        </div>

        <!-- Submit Button -->
        
    </form>

    <!-- Resend OTP Form -->
    <form action="{{ route('resendOtp') }}" method="POST" id="resendForm" class="hidden">
        @csrf
        <input type="hidden" name="token_id" value="{{ Crypt::encrypt($user_id) }}">
        <input type="hidden" name="source_type" value="{{ Crypt::encrypt($source_type) }}">
    </form>

    <!-- Scripts -->
    <script>
        function resendOtp() {
            document.getElementById("resendForm").submit();
        }
        function encryptPasswordsforLoginForm() {
            encryptPasswords(
                document.getElementById('password'),
            );
        }
    </script>
</x-layouts.guest>
