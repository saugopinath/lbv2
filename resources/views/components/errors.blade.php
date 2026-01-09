@if ($errors->any())
    <div class="rounded-md p-2 bg-green-100 text-green-800 border border-green-200">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif