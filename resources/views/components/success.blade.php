@if ( $message = Session::get('success'))

<div class="rounded p-3 bg-success/10 text-success">
   
        {{ $message}}
    </div>
@endif