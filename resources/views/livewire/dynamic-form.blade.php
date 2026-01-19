<div class="space-y-6 mt-6">

    @foreach($views as $view)
        <div class="border rounded p-4">

            <h3 class="font-semibold text-lg mb-3">
                {{ $tabNames[$view] ?? 'Unknown Tab' }}
            </h3>

            @includeIf("schemes.scheme_{$schemeId}.{$view}")

        </div>
    @endforeach

</div>
