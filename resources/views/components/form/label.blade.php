@props(['name', 'label' => ''])

<label {{ $attributes->merge(['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white', 'for'=> $name]) }}>
    {!! empty($label) ? trim(str_replace('id','',str_replace('_',' ',$name))) : $label !!}
</label>