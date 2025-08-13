
<!-- <div class="flex w-full sm:w-[45%] h-14 border border-gray-300 rounded-xl overflow-hidden"> -->
    <div id="captcha-container" class="w-full sm:w-[44%] h-12 object-contain border sm:border-y border-gray-300 rounded-xl sm:rounded-xl">
        {!! captcha_img('math') !!}
    </div>
       <button type="button"
        onclick="refreshCaptcha()"
        {{ $attributes->merge([
            'class' => 'w-full sm:w-[16%] h-12 bg-[#0f6dfad6] flex items-center justify-center rounded-xl sm:rounded-r-xl sm:rounded-l-none border border-gray-300'
        ]) }}>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26" width="20" height="20" fill="currentColor">
            <path d="M13.8 0C7.88 0 4.08 4.29 4 10H0.5c-.2 0-.4.11-.4.31 0 .2 0 .4.1.5l6 7.7c.1.1.2.2.4.2s.3-.1.4-.2l6-7.7c.1-.1.1-.3 0-.5-.1-.2-.3-.3-.4-.3H9C9.07 2.46 12.92.79 13.81.09 14.01 0 14.01 0 13.81 0zM19.5 7.34c-.15 0-.3.05-.4.16l-6 7.69c-.1.2-.1.4 0 .5.1.2.3.3.4.3H17c-.07 7.54-3.93 9.21-4.82 9.91-.2.1-.2.1 0 .1 5.93 0 9.73-4.29 9.81-10h3.4c.2 0 .4-.11.4-.31 0-.2 0-.4-.1-.5l-6-7.69c-.1-.11-.25-.16-.4-.16z"/>
        </svg>
    </button>
<!-- </div> -->

<script>
    function refreshCaptcha() {
        fetch("{{ route('refresh-captcha') }}")
            .then(response => response.text())
            .then(data => {
                document.getElementById('captcha-container').innerHTML = data;
            });
    }
</script>
