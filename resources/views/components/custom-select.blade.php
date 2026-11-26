@props([
    'name',
    'id' => null,
    'value' => null,
    'options' => [],
    'valueKey' => 'id',
    'labelKey' => 'name',
    'placeholder' => null,
    'submitOnChange' => false,
    'required' => false,
    'size' => 'md', // md (h-11), sm (h-9 or h-9.5)
])

@php
    $inputId = $id ?? $name;
    $currentValue = old($name, $value ?? '');

    $normalized = [];
    if ($placeholder !== null) {
        $normalized[] = ['value' => '', 'label' => $placeholder];
    }

    $isAssoc = false;
    if (is_array($options)) {
        // Cek apakah associative array (kunci string atau kunci angka non-sekuensial 0..n-1)
        $keys = array_keys($options);
        $isAssoc = $keys !== range(0, count($options) - 1);
    }

    foreach ($options as $k => $item) {
        if (is_array($item)) {
            $val = $item[$valueKey] ?? $item['value'] ?? $k;
            $lbl = $item[$labelKey] ?? $item['label'] ?? $val;
        } elseif (is_object($item)) {
            $val = $item->{$valueKey} ?? $item->id ?? (string) $k;
            $lbl = $item->{$labelKey} ?? $item->title ?? $item->name ?? (string) $val;
        } else {
            // Jika array asosiatif (misal [1 => 'Januari', 'Present' => 'Hadir']), key adalah value dan item adalah label.
            // Jika list terindeks murni ['Tetap', 'Kontrak'], item adalah value dan label.
            $val = $isAssoc ? $k : $item;
            $lbl = $item;
        }
        $normalized[] = [
            'value' => (string) $val,
            'label' => (string) $lbl,
        ];
    }
@endphp

<div x-data="{
    open: false,
    selected: @js((string) $currentValue),
    options: @js($normalized),
    submitOnChange: @js((bool) $submitOnChange),

    get displayLabel() {
        const found = this.options.find(o => String(o.value) === String(this.selected));
        return found ? found.label : (@js($placeholder) || 'Pilih...');
    },
    selectOption(val) {
        this.selected = val;
        this.open = false;
        this.$nextTick(() => {
            const hiddenInput = this.$refs.hiddenInput;
            if (hiddenInput) {
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (this.submitOnChange) {
                const form = this.$el.closest('form');
                if (form) form.submit();
            }
        });
    }
}"
x-modelable="selected"
{{ $attributes->merge(['class' => 'relative w-full text-left']) }}
@click.outside="open = false"
@keydown.escape.window="open = false">

    <!-- Hidden input for standard HTTP form submit -->
    <input type="hidden"
           name="{{ $name }}"
           id="{{ $inputId }}"
           x-ref="hiddenInput"
           :value="selected"
           @if($required) required @endif
           value="{{ (string) $currentValue }}">

    <!-- Trigger Button -->
    <button type="button"
            @click="open = !open"
            aria-haspopup="listbox"
            :aria-expanded="open"
            class="w-full flex items-center justify-between gap-2 px-3.5 bg-white border border-slate-300 hover:border-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 rounded-xl text-slate-800 transition shadow-xs {{ $size === 'sm' ? 'h-9 text-xs' : 'h-11 text-sm' }}">
        <span class="truncate font-medium text-left"
              :class="selected === '' && @js($placeholder !== null) ? 'text-slate-400 font-normal' : 'text-slate-800'"
              x-text="displayLabel">
            {{ collect($normalized)->firstWhere('value', (string)$currentValue)['label'] ?? ($placeholder ?: 'Pilih...') }}
        </span>
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200 stroke-[2]"
             :class="{ 'rotate-180 text-indigo-600': open }"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Floating Dropdown Menu Panel -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
         class="absolute z-50 left-0 right-0 mt-1.5 bg-white rounded-xl border border-slate-200 shadow-lg max-h-60 overflow-y-auto py-1 text-xs sm:text-sm">
        <template x-for="item in options" :key="item.value">
            <button type="button"
                    @click="selectOption(item.value)"
                    class="w-full flex items-center justify-between px-3.5 py-2 text-left transition"
                    :class="String(selected) === String(item.value) ? 'bg-indigo-50/80 text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-50'">
                <span class="truncate" x-text="item.label"></span>
                <svg x-show="String(selected) === String(item.value)"
                     class="w-4 h-4 text-indigo-600 flex-shrink-0 ml-2 stroke-[2.5]"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </template>
    </div>
</div>
