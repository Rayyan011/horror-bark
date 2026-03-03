@props([
    'entityType',
    'entityId',
    'inputName' => 'booking_time',
    'idPrefix' => 'slot',
])

<div
    x-data="{
        date: '',
        slots: [],
        loading: false,
        selected: null,
        error: null,
        async fetchSlots() {
            if (!this.date) return;
            this.loading = true;
            this.error = null;
            this.selected = null;
            try {
                const res = await fetch(`{{ route('api.slots') }}?type={{ $entityType }}&id={{ $entityId }}&date=${this.date}`);
                const data = await res.json();
                this.slots = data.slots || [];
            } catch (e) {
                this.error = 'Failed to load availability.';
                this.slots = [];
            }
            this.loading = false;
        },
        selectSlot(slot) {
            if (!slot.available) return;
            this.selected = slot.datetime;
        }
    }"
    class="space-y-3"
>
    <label class="block text-sm font-medium text-gray-300">Select Date</label>
    <input
        type="date"
        x-model="date"
        @change="fetchSlots()"
        class="w-full rounded-md border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-gray-200 focus:border-amber-500 focus:ring-amber-500"
        min="{{ now()->toDateString() }}"
        id="{{ $idPrefix }}_slot_date"
    />

    <template x-if="loading">
        <p class="text-sm text-gray-400 animate-pulse">Loading availability...</p>
    </template>

    <template x-if="error">
        <p class="text-sm text-red-400" x-text="error"></p>
    </template>

    <template x-if="!loading && slots.length > 0">
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Available Slots</label>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="slot in slots" :key="slot.time">
                    <button
                        type="button"
                        @click="selectSlot(slot)"
                        :disabled="!slot.available"
                        :class="{
                            'border-amber-500 bg-amber-500/20 text-amber-300': selected === slot.datetime,
                            'border-gray-600 bg-gray-800 text-gray-300 hover:border-gray-400': selected !== slot.datetime && slot.available,
                            'border-gray-700 bg-gray-900 text-gray-600 cursor-not-allowed': !slot.available,
                        }"
                        class="rounded-lg border px-3 py-2 text-sm font-medium transition-colors text-center"
                    >
                        <span x-text="slot.time" class="block text-base font-bold"></span>
                        <span
                            x-text="slot.available ? slot.remaining + ' left' : 'Full'"
                            :class="slot.available ? 'text-green-400' : 'text-red-400'"
                            class="block text-xs mt-0.5"
                        ></span>
                    </button>
                </template>
            </div>
        </div>
    </template>

    <template x-if="!loading && date && slots.length === 0 && !error">
        <p class="text-sm text-gray-400">No slots available for this date.</p>
    </template>

    <input type="hidden" name="{{ $inputName }}" :value="selected" id="{{ $idPrefix }}_slot_value" />
</div>
