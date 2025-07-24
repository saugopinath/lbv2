@props(['message'])

<div class="flex items-center justify-center mt-4 mb-4 text-gray-700">
    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-gray-400 border-dashed rounded-md opacity-50 sm:w-full md:w-96 md:h-48">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
          </svg>
        <h2>{{ $message }}</h2>
    </div>
</div>
