     <form x-data="locationForm()"
         x-init="init()" x-on:submit.prevent="
            Livewire.dispatch('showLoader');
            $wire.save();
        ">
         <div class="bg-white shadow rounded-xl p-2 mb-2">
             @foreach ($fields as $sectionId => $groupFields)
             @php
             $section = $sections[$sectionId] ?? null;
             @endphp
             {{-- ================= SECTION EXISTS ================= --}}
             @if ($section)
             <fieldset class="border border-gray-300 rounded-lg pl-4 pr-4 mb-2">
                 <legend class="text-lg font-semibold mb-1 text-indigo-700">
                     {{ $section['section_name'] }}
                 </legend>
                 <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-2">
                     @foreach ($groupFields as $field)
                     <div>
                         @if ($field['field_type'] === 'text'|| $field['field_type'] === 'date' )
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}" required
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- NUMBER --}}
                         @if ($field['field_type'] === 'textarea')
                         <x-form.textarea name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}" required
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- NUMBER --}}
                         @if ($field['field_type'] === 'number')
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}" required
                             type="{{ $field['field_type'] }}" />
                         @endif
                         @if ($field['field_type'] === 'file')
                         <x-form.input
                             name="{{ $field['field_label'] }}"
                             id="{{ $field['field_id'] }}"
                             wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}"
                             type="file"
                             required />
                         @endif
                         @if ($field['field_type'] === 'password')
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}" required
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- SELECT (MULTIPLE) --}}
                         @if ($field['field_type'] === 'select' && $field['is_multiple'] === false)
                         <x-form.select name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" label="{{ $field['level_name'] }}" wire:model="formData.{{ $field['field_label'] }}">
                             <option value="">-- Select {{ $field['field_label'] }} --</option>
                             @foreach ($field['options'] as $option)
                             <option value="{{ $option }}">{{ $option }}</option>
                             @endforeach
                         </x-form.select>
                         @elseif ($field['field_type'] === 'select' && $field['is_multiple'] === true)
                         <x-form.multiselect
                             name="{{ $field['field_label'] }}[]"
                             id="{{ $field['field_id'] }}"
                             label="{{ $field['level_name'] }}"
                             wire:model="formData.{{ $field['field_label'] }}"
                             :options="$field['options']"
                             multiple />
                         @endif
                         {{-- RADIO (treated as multiple) --}}
                         @if ($field['field_type'] === 'radio')
                         <x-form.label name="{{ $field['level_name'] }}" class="mt-3" />
                         <div class="flex flex-wrap items-center gap-4">
                             @foreach ($field['options'] as $option)
                             <x-form.radio
                                 name="{{ $field['field_label'] }}"
                                 value="{{ $option }}"
                                 label="{{ $option }}"
                                 wire:model="formData.{{ $field['field_label'] }}" />
                             @endforeach
                         </div>
                         {{-- VALIDATION ERROR --}}
                         <x-form.error name="formData.{{ $field['field_label'] }}" />
                         @endif
                         {{-- CHECKBOX --}}
                         @if ($field['field_type'] === 'checkbox')
                         <x-form.label name="{{ $field['level_name'] }}" />
                         <div class="space-y-2">
                             @foreach ($field['options'] as $option)
                             <x-form.checkbox
                                 name="{{ $field['field_id'] }}[]"
                                 value="{{ $option }}"
                                 label="{{ $option }}"
                                 wire:model="formData.{{ $field['field_label'] }}" />
                             @endforeach
                         </div>
                         <x-form.error name="formData.{{ $field['field_label'] }}" />
                         @endif
                     </div>
                     @endforeach
                 </div>
             </fieldset>
             @else
             {{-- Divider --}}
             <div class="flex items-center my-8">
                 <div class="flex-grow border-t border-dashed border-gray-400"></div>
             </div>
             <div class="bg-gray-50 rounded-xl p-6 mb-8">
                 <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                     @foreach ($groupFields as $field)
                     <div>
                         @if ($field['field_type'] === 'text'|| $field['field_type'] === 'date' )
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}"
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- NUMBER --}}
                         @if ($field['field_type'] === 'textarea')
                         <x-form.textarea name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}"
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- NUMBER --}}
                         @if ($field['field_type'] === 'number')
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}"
                             type="{{ $field['field_type'] }}" />
                         @endif
                         @if ($field['field_type'] === 'file')
                         <x-form.input
                             name="{{ $field['field_label'] }}"
                             id="{{ $field['field_id'] }}"
                             wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}"
                             type="file" />
                         @endif
                         @if ($field['field_type'] === 'password')
                         <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" wire:model="formData.{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}" placeholder="Enter {{ $field['level_name'] }}"
                             type="{{ $field['field_type'] }}" />
                         @endif
                         {{-- SELECT (MULTIPLE) --}}
                         @if ($field['field_type'] === 'select' && $field['is_multiple'] === false)
                         @if($field['field_class'])
                         <x-form.select
                             name="{{ $field['field_label'] }}"
                             label="{{ $field['level_name'] }}"
                             wire:ignore
                             x-model="models['{{ str_replace('/', '_', $field['field_class']) }}']">
                             <option value="">-- Select {{ $field['level_name'] }} --</option>

                             <template x-for="opt in getOptions('{{ $field['field_class'] }}')" :key="opt.id">
                                 <option :value="opt.id" x-text="opt.text"></option>
                             </template>
                         </x-form.select>
                         @else
                         <x-form.select name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}" label="{{ $field['level_name'] }}" wire:model="formData.{{ $field['field_label'] }}">
                             <option value="">-- Select {{ $field['field_label'] }} --</option>
                             @foreach ($field['options'] as $key => $option)
                             <option value="{{ $key }}">{{ $option }}</option>
                             @endforeach

                         </x-form.select>
                         @endif

                         @elseif ($field['field_type'] === 'select' && $field['is_multiple'] === true)
                         <x-form.multiselect
                             name="{{ $field['field_label'] }}[]"
                             id="{{ $field['field_id'] }}"
                             label="{{ $field['level_name'] }}"
                             wire:model="formData.{{ $field['field_label'] }}"
                             :options="$field['options']"
                             multiple />

                         @endif
                         {{-- RADIO (treated as multiple) --}}
                         @if ($field['field_type'] === 'radio')
                         <x-form.label name="{{ $field['level_name'] }}" class="mt-3" />

                         <div class="flex flex-wrap items-center gap-4">
                             @foreach ($field['options'] as $option)
                             <x-form.radio
                                 name="{{ $field['field_label'] }}"
                                 value="{{ $option }}"
                                 label="{{ $option }}"
                                 wire:model="formData.{{ $field['field_label'] }}" />
                             @endforeach
                         </div>
                         @endif
                         {{-- CHECKBOX --}}
                         @if ($field['field_type'] === 'checkbox')
                         <x-form.label name="{{ $field['level_name'] }}" />

                         <div class="space-y-2">
                             @foreach ($field['options'] as $option)
                             <x-form.checkbox
                                 name="{{ $field['field_label'] }}[]"
                                 value="{{ $option }}"
                                 label="{{ $option }}"
                                 wire:model="formData.{{ $field['field_label'] }}" />
                             @endforeach

                         </div>
                         @endif
                     </div>
                     @endforeach

                 </div>
             </div>
             @endif
             @endforeach
             <div class="flex justify-between mt-4 pl-6 pr-6">
                 @if ($mode != '0')
                 <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
                 @endif
                 <x-button.success type="submit">
                     {{ $mode == '0' ? 'Save' : 'Preview and Submit' }}
                 </x-button.success>
             </div>

         </div>
     </form>
     <script>
         function locationForm() {
             return {
                 // ===== MODELS =====
                 models: {
                     district: '',
                     assemblies: '',
                     rural_urban: '',
                     localbody: '',
                     panchayat: ''
                 },

                 // ===== MASTER DATA =====
                 districts: [],
                 assemblies: [],
                 blocks: [],
                 gps: [],
                 ulbs: [],
                 ulbWards: [],

                 // ===== FILTERED =====
                 assembliesFiltered: [],
                 localBodiesFiltered: [],
                 gpFiltered: [],

                 init() {
                     const md = window.masterDataV2 || {};

                     this.districts = md.districts || [];
                     this.assemblies = md.assemblies || [];
                     this.blocks = md.blocks || [];
                     this.gps = md.gps || [];
                     this.ulbs = md.ulbs || [];
                     this.ulbWards = md.ulb_wards || [];

                     // ✅ DISTRICT → ASSEMBLY
                     this.$watch('models.district', v => {
                         this.models.assemblies = '';
                         this.models.rural_urban = '';
                         this.models.block = '';
                         this.models.panchayat = '';

                         this.assembliesFiltered =
                             this.assemblies.filter(a => a.district_code == v);

                         this.$wire?.set('formData.district', v);
                     });

                     // ✅ RURAL / URBAN → BLOCK / ULB
                     this.$watch('models.rural_urban', v => {
                         this.models.block = '';
                         this.models.panchayat = '';

                         if (v == 2) {
                             this.localBodiesFiltered =
                                 this.blocks.filter(b => b.district_code == this.models.district);
                         } else if (v == 1) {
                             this.localBodiesFiltered =
                                 this.ulbs.filter(u => u.district_code == this.models.district);
                         } else {
                             this.localBodiesFiltered = [];
                         }

                         this.$wire?.set('formData.rural_urban', v);
                     });

                     // ✅ BLOCK → GP
                     this.$watch('models.block', v => {
                         this.models.panchayat = '';
                         this.gpFiltered = [];

                         if (this.models.rural_urban == 2) {
                             // Rural → GP
                             this.gpFiltered =
                                 this.gps.filter(g =>
                                     g.district_code == this.models.district &&
                                     g.block_code == v
                                 );
                         } else if (this.models.rural_urban == 1) {
                             // Urban → Ward
                             this.gpFiltered =
                                 this.ulbWards.filter(w => w.urban_body_code == v);
                         }

                         this.$wire?.set('formData.block', v);
                     });

                     // ✅ GP
                     this.$watch('models.panchayat', v => {
                         this.$wire?.set('formData.panchayat', v);
                     });
                 },

                 // ===== OPTIONS =====
                 getOptions(fieldClass) {
                     switch (fieldClass) {
                         case 'district':
                             return this.districts;

                         case 'assemblies':
                             return this.assembliesFiltered;

                         case 'rural/urban':
                             return [{
                                     id: 1,
                                     text: 'Urban'
                                 },
                                 {
                                     id: 2,
                                     text: 'Rural'
                                 }
                             ];

                         case 'block':
                             return this.localBodiesFiltered;

                         case 'panchayat':
                             return this.gpFiltered;

                         default:
                             return [];
                     }
                 }
             }
         }
     </script>