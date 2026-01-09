<div class="bg-gray-100 p-4 rounded shadow mb-4">
    <form wire:submit.prevent="submit">
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input id="fullname" name="fullname" label="Full Name" placeholder="Enter Full Name" required
                    wire:model="fullname" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input id="fullnameaadhar" name="fullnameaadhar" label="Full Name as in Aadhaar"
                    placeholder="Enter Full Name as in Aadhaar" required wire:model="fullnameaadhar"
                    x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input id="displayname" name="displayname" label="Display Name" placeholder="Enter Display Name"
                    required wire:model="displayname" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input id="email" name="email" type="email" label="Email address" required
                    wire:model="email" placeholder="example@example.com" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input id="mobile" name="mobile" label="Mobile Number" required wire:model="mobile"
                    placeholder="123-45-678" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
            </div>
            <div>
                <x-form.select name="role" id="role" label="Select Role" required wire:model="role">
                    <option value="">Select</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
        <x-button.primary wire:click="submit" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
            Create
        </x-button.primary>
    </form>
</div>
