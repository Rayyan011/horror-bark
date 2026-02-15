@props([
    'title',
    'subtitle' => null,
    'action' => null,
    'method' => 'POST',
    'fields' => [],
    'links' => [],
    'submitLabel' => 'Submit',
])

<main class="max-w-lg mx-auto">
    <x-ui.surface>
        <h1 class="text-3xl font-bold mb-6 horror-font text-center">{{ $title }}</h1>

        @if ($subtitle)
            <p class="text-sm text-gray-300 text-center mb-6">{{ $subtitle }}</p>
        @endif

        @if ($action)
            <x-ui.form :action="$action" :method="$method" class="space-y-4">
                @isset($beforeFields)
                    {{ $beforeFields }}
                @endisset

                @foreach ($fields as $field)
                    @php
                        $fieldType = $field['type'] ?? 'text';
                    @endphp

                    @if ($fieldType === 'hidden')
                        <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] ?? '' }}" />
                    @elseif ($fieldType === 'select')
                        <x-ui.select
                            :label="$field['label']"
                            :name="$field['name']"
                            :options="$field['options'] ?? []"
                            :value="$field['value'] ?? null"
                            :placeholder="$field['placeholder'] ?? null"
                            :required="$field['required'] ?? false"
                        />
                    @elseif ($fieldType === 'textarea')
                        <x-ui.textarea
                            :label="$field['label']"
                            :name="$field['name']"
                            :rows="$field['rows'] ?? 4"
                            :value="$field['value'] ?? null"
                            :required="$field['required'] ?? false"
                        />
                    @elseif ($fieldType === 'checkbox')
                        <label class="inline-flex items-center text-sm">
                            <input
                                type="checkbox"
                                name="{{ $field['name'] }}"
                                value="{{ $field['checkbox_value'] ?? '1' }}"
                                class="mr-2 rounded bg-gray-900 border-gray-700"
                                @if (!empty($field['checked'])) checked @endif
                            />
                            {{ $field['label'] }}
                        </label>
                    @else
                        <x-ui.field
                            :label="$field['label']"
                            :name="$field['name']"
                            :type="$fieldType"
                            :value="$field['value'] ?? null"
                            :required="$field['required'] ?? false"
                            :placeholder="$field['placeholder'] ?? null"
                        />
                    @endif
                @endforeach

                {{ $slot }}

                <x-ui.button type="submit" block>{{ $submitLabel }}</x-ui.button>
            </x-ui.form>
        @else
            {{ $slot }}
        @endif

        @if (!empty($links))
            <div class="mt-4 text-sm text-center text-gray-300 space-y-2">
                @foreach ($links as $link)
                    <p>
                        @if (!empty($link['prefix']))
                            <span>{{ $link['prefix'] }} </span>
                        @endif
                        <a href="{{ $link['href'] }}" class="text-red-400 hover:text-red-300">{{ $link['label'] }}</a>
                    </p>
                @endforeach
            </div>
        @endif
    </x-ui.surface>
</main>
