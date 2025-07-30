<!-- <x-layouts.guest1>
    <!-- Session Status -->

    <form class="w-full space-y-6" action="{{ route('loginPost') }}" method="POST">
        @csrf
    <input id="mobile" type="text" name="mobile":value="old('mobile_no')"autofocus autocomplete="off"  maxlength="10" placeholder="Registered Mobile No"
    class="w-full px-4 h-14 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"/>
    
    </form>
    
    <!-- <script>
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
    </script> -->
</x-layouts.guest1> -->