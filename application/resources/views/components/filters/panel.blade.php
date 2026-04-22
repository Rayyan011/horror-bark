@props([
    'fields' => [],
    'applyLabel' => 'Apply',
    'resetHref' => null,
    'method' => 'GET',
    'action' => null,
    'hidden' => [],
    'grid' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4',
])

@php
    $formAction = $action ?: url()->current();
@endphp

<x-ui.surface variant="default" padding="p-0" class="mb-8 overflow-hidden">
    <x-ui.form :method="$method" :action="$formAction" :csrf="strtoupper($method) !== 'GET'" class="catalog-filter-shell space-y-5">
        @foreach ($hidden as $name => $value)
            @if (!is_null($value) && $value !== '')
                <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
            @endif
        @endforeach

        <div class="{{ $grid }}">
            @foreach ($fields as $field)
                @php
                    $type = $field['type'] ?? 'text';
                    $fieldClass = $field['class'] ?? '';
                @endphp

                @if ($type === 'select')
                    <x-ui.select
                        :label="$field['label']"
                        :name="$field['name']"
                        :options="$field['options'] ?? []"
                        :value="$field['value'] ?? null"
                        :placeholder="$field['placeholder'] ?? null"
                        :class="$fieldClass"
                    />
                @elseif ($type === 'range')
                    <x-ui.range-slider
                        :label="$field['label']"
                        :name="$field['name']"
                        :value="$field['value'] ?? null"
                        :min="$field['min'] ?? 0"
                        :max="$field['max'] ?? 100"
                        :step="$field['step'] ?? 1"
                        :prefix="$field['prefix'] ?? ''"
                        :suffix="$field['suffix'] ?? ''"
                        :hint="$field['hint'] ?? null"
                        :class="$fieldClass"
                    />
                @elseif ($type === 'range_pair')
                    <x-ui.range-pair
                        :label="$field['label']"
                        :min-name="$field['min_name']"
                        :max-name="$field['max_name']"
                        :min-value="$field['min_value'] ?? null"
                        :max-value="$field['max_value'] ?? null"
                        :min="$field['min'] ?? 0"
                        :max="$field['max'] ?? 100"
                        :step="$field['step'] ?? 1"
                        :prefix="$field['prefix'] ?? ''"
                        :suffix="$field['suffix'] ?? ''"
                        :hint="$field['hint'] ?? null"
                        :class="$fieldClass"
                    />
                @elseif ($type === 'textarea')
                    <x-ui.textarea
                        :label="$field['label']"
                        :name="$field['name']"
                        :value="$field['value'] ?? null"
                        :rows="$field['rows'] ?? 4"
                        :required="$field['required'] ?? false"
                        :class="$fieldClass"
                    />
                @else
                    <x-ui.field
                        :label="$field['label']"
                        :name="$field['name']"
                        :type="$type"
                        :value="$field['value'] ?? null"
                        :placeholder="$field['placeholder'] ?? null"
                        :required="$field['required'] ?? false"
                        :hint="$field['hint'] ?? null"
                        :min="$field['min'] ?? null"
                        :max="$field['max'] ?? null"
                        :step="$field['step'] ?? null"
                        :class="$fieldClass"
                    />
                @endif
            @endforeach
        </div>

        <x-ui.form-actions :submit-label="$applyLabel" :reset-href="$resetHref" class="pt-2" />
    </x-ui.form>
</x-ui.surface>
