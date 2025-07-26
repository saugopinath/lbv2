<div class="max-w-xl">
    <h3 class="text-lg font-semibold">Enter Aadhaar</h3>
    <form class="mt-[5px] grid grid-cols-1 sm:grid-cols-2 gap-5">
        <x-text-input id="aadhaar" class="form-input h-[66px] bg-transparent dark:bg-transparent text-base rounded-[10px] ps-5 pe-14" type="text" name="aadhaar" autofocus autocomplete="off" placeholder="Aadhaar Number" maxlength="12" required/>
        <x-button.danger type="submit">
            Check Aadhaar
        </x-button.danger>
    </form>
</div>