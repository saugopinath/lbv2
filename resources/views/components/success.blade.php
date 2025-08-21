@if ( $message = Session::get('success'))

<div class="rounded-md p-2 bg-green-100 text-green-800 border border-green-200">
        {{ $message}}
    </div>
@endif