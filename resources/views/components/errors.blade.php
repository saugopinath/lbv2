@if ($errors->any())
    <div class="rounded p-3 bg-danger/10 text-danger mt-6">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif