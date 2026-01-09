<x-layouts.guest>
    <!-- Session Status -->
    <x-errors class="mt-4" />
    <form class="mt-4 space-y-5" action="{{ route('resetPasswordPost') }}" method="POST">
        @csrf
        <input type="hidden" name="token_id" value="{{Crypt::encrypt($user_id)}}">
        <input type="hidden" name="source_type" value="{{Crypt::encrypt($source_type)}}">

        <div>
            <x-publicForm.password-input
                id="password"
                name="user_password"
                type="password"
                :value="old('user_password')"
                autofocus autocomplete="off"
                placeholder="Enter New Password"
                class="w-full px-5 h-12 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg" />
        </div>
        <div>
            <x-publicForm.password-input
                id="password"
                type="password"
                name="confirm_user_password"
                :value="old('confirm_user_password')"
                autofocus autocomplete="off"
                placeholder="Confirm Password"
                class="w-full px-5 h-12 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg" />
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <x-publicForm.text-input id="captcha" class="w-full sm:w-[55%] h-12 px-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="captcha" :value="old('captcha')"
                autofocus autocomplete="off" placeholder="Captcha" />
            <x-publicForm.captcha />
        </div>

         <x-publicForm.button type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200">
               Reset Password
            </x-publicForm.button>

       
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