<x-layouts.guest>
    <!-- Session Status -->


    <x-errors class="mt-6"  />
    <x-success class="mt-6"  />
    <form class="mt-[60px] space-y-5" action="{{ route('loginPost') }}" method="POST">
        @csrf

          <div>
                            <label for="mobile"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mobile Number</label>
                            {{--  <input type="text" name="mobile_no" :value="old('mobile_no')" id="mobile"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200"
                                placeholder="Please Enter Your  Registered Mobile Number" required autofocus value="{{ old('mobile') }}" maxlength="10">  --}}

                                 <x-text-input id="mobile"  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200" type="text" name="mobile_no" :value="old('mobile_no')"
                autofocus autocomplete="off" placeholder="Please Enter Your  Registered Mobile Number" maxlength="10"/>
                        </div>
                        <!-- Password -->

  <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <div class="relative">
                                <input type="password" name="password" :value="old('password')" id="password"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all duration-200"
                                    placeholder="Please Enter Password" required value="{{ old('password') }}">


                            </div>

                        </div>
     <!-- Captcha -->
                          <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white">Captcha
                                Verification</label>
                            <div class="flex space-x-4">
                             <x-text-input id="captcha" type="text" name="captcha" :value="old('captcha')"

                                    class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter Captcha" required/>

                                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-4"
                                    style="min-width: 160px">
                                    <div id="captcha-container" class="flex items-center justify-center"
                                        style="width: 120px; height: 40px">
                                        {!! captcha_img('math') !!}
                                    </div>
                                    <button type="button" onclick="refreshCaptcha()"
                                        class="ml-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                       <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 26 26" width="26px" height="26px"><path d="M 13.8125 0 C 7.878906 0 4.082031 4.292969 4 10 L 0.5 10 C 0.300781 10 0.09375 10.113281 0.09375 10.3125 C -0.0078125 10.511719 -0.0078125 10.710938 0.09375 10.8125 L 6.09375 18.5 C 6.195313 18.601563 6.300781 18.6875 6.5 18.6875 C 6.699219 18.6875 6.804688 18.601563 6.90625 18.5 L 12.90625 10.8125 C 13.007813 10.710938 13.007813 10.511719 12.90625 10.3125 C 12.804688 10.113281 12.601563 10 12.5 10 L 9 10 C 9.066406 2.464844 12.921875 0.789063 13.8125 0.09375 C 14.011719 -0.0078125 14.011719 0 13.8125 0 Z M 19.5 7.34375 C 19.351563 7.34375 19.195313 7.398438 19.09375 7.5 L 13.09375 15.1875 C 12.992188 15.386719 13 15.585938 13 15.6875 C 13.101563 15.886719 13.304688 16 13.40625 16 L 17 16 C 16.933594 23.535156 13.078125 25.210938 12.1875 25.90625 C 11.988281 26.007813 11.988281 26 12.1875 26 C 18.121094 26 21.917969 21.707031 22 16 L 25.40625 16 C 25.605469 16 25.8125 15.886719 25.8125 15.6875 C 26.011719 15.488281 26.007813 15.289063 25.90625 15.1875 L 19.90625 7.5 C 19.804688 7.398438 19.648438 7.34375 19.5 7.34375 Z"/></svg>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button -->
<div class="!mt-[50px] flex justify-center">
  <button
    type="submit" class="ml-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
    Log In
  </button>

</div>


        <div class="pt-3 font-medium text-gray-500 dark:text-gray-400 flex items-center justify-center text-md ">
            <a href="{{route('forget-password')}}" class="hover:text-primary duration-300">Forgot Password?</a>
        </div>

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
