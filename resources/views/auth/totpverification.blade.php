<x-layouts.guest>

    <x-errors class="mt-6" />
    <x-success class="mt-4" />

    <div class="mt-6">
        <h2 class="text-xl font-semibold text-center">
            TOTP Verification
        </h2>
       {{--
        <p class="mt-2 text-sm text-gray-600 text-center">
            Add this secret to your Authenticator app, then enter the 6-digit code.
        </p>

        <div class="mt-4 p-4 bg-gray-100 rounded-xl text-center">
            <p class="text-sm text-gray-600">Secret Key</p>

            <p class="mt-1 font-mono font-semibold break-all">
                {{ $secret }}
            </p>
        </div>
        --}}
       <script>
    window.history.replaceState(null, '', '{{ route('login') }}');
</script>
        <form class="mt-6 space-y-4"
              action="{{ route('totp-validate-post') }}"
              method="POST">

            @csrf

            <input type="hidden"
                   name="token_id"
                   value="{{ $token_id }}">

            <x-publicForm.text-input
                id="code"
                name="code"
                placeholder="Enter TOTP"
                maxlength="6"
                inputmode="numeric"
                autocomplete="off"
                class="w-full h-12 px-5 border border-gray-300 rounded-2xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
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

           <x-publicForm.button type="submit"
    class="w-full h-12 bg-blue-600 hover:bg-blue-700 !text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200 flex justify-center items-center gap-3">
    Validate TOTP
</x-publicForm.button>
<a href="{{ route('verification', ['token_id' => $token_id, 'resend' => 1]) }}"
   style="margin-top: 20px;"
    class="w-full h-12 bg-green-600 hover:bg-green-700 !text-white text-lg font-semibold rounded-xl shadow-md transition-colors duration-200 flex justify-center items-center gap-3">
    Resend TOTP
</a>
<div class="flex justify-center items-center gap-2 mt-2">
    <a href="{{ route('login') }}"
       class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium">
        ← Back to Login
    </a>
</div>
        </form>
    </div>

</x-layouts.guest>