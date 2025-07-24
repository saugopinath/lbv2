@props(['name', 'label' => ''])

<label {{ $attributes->merge(['class' => 'block mb-2 uppercase font-bold text-xs text-gray-700', 'for'=> $name]) }}>
    {{ empty($label) ? trim(str_replace('id','',str_replace('_',' ',$name))) : $label}}
</label>