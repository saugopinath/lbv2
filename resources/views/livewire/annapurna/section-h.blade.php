<div class="space-y-6">
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5">
        <div class="border-b-2 border-indigo-900 pb-2 mb-4">
            <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                <span
                    class="text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
                    style="background-color: #78350f;">H</span>
                Declaration & Consent | ঘোষণা এবং সম্মতি
            </h3>
        </div>

        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <input type="checkbox" wire:model="formData.agree_consent" id="agree_consent"
                    class="mt-1 h-4 w-4 text-indigo-900 border-gray-300 rounded focus:ring-indigo-500">
                <label for="agree_consent"
                    class="text-xs md:text-sm text-gray-700 font-medium leading-relaxed">
                    I hereby declare that the above information is true to the best of my knowledge
                    and I have provided all the supporting documents where applicable and HAVE NOT
                    missed any criteria as mentioned above. I understand that my social protection
                    benefits will be stopped if any information provided by me turns out to be
                    false.
                    <br>
                    <span class="text-xs text-gray-500 font-normal italic">
                        আমি ঘোষণা করছি যে আমার জ্ঞানত উপরোক্ত তথ্যগুলি সত্য এবং আমি প্রযোজ্য সমস্ত
                        সহায়ক নথি প্রদান করেছি। আমি বুঝতে পারছি যে আমার দেওয়া কোনো তথ্য ভুল প্রমানিত
                        হলে আমার সামাজিক সুরক্ষা সুবিধা বন্ধ করে দেওয়া হবে।
                    </span>
                </label>
            </div>
            @error('formData.agree_consent')
                <div class="text-red-600 text-xs pl-7 font-semibold">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
