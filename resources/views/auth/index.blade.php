<x-layouts.guest>
    <x-errors class="mt-6" />
    <x-success class="mt-6" />

    <form class="mt-4 space-y-5" action="{{ route('loginPost') }}" method="POST">
        @csrf

        <x-publicForm.text-input
            id="mobile"
            name="mobile_no"
            placeholder="Mobile Number"
            maxlength="10"
            :value="old('mobile_no')"
            class="w-full px-5 h-14 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
            required
        />

        <x-publicForm.password-input
            id="password"
            name="password"
            placeholder="Password"
            class="w-full px-5 h-14 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
        />

        <div class="flex flex-col sm:flex-row items-center gap-2">
            <x-publicForm.text-input
                id="captcha"
                name="captcha"
                placeholder="Captcha"
                :value="old('captcha')"
                class="w-full sm:w-[55%] h-14 px-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />
            <x-publicForm.captcha class="w-full sm:w-[44%] h-14 object-contain border sm:border-y border-gray-300 rounded-xl sm:rounded-none"/>
        </div>
        <x-publicForm.button class="w-full h-14 bg-[#0e3e98f0] text-white text-lg font-semibold rounded-xl shadow-md hover:bg-[#0c3591] flex justify-center items-center gap-3">Send OTP</x-publicForm.button>
       
    </form> 
    <div class="text-right mt-2 text-blue-600 italic text-sm hover:underline cursor-pointer">
        <a href="{{ route('forget-password') }}">Forgot Password?</a>
    </div>
</x-layouts.guest>
