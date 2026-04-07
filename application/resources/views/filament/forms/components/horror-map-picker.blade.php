@php
    $zones = \App\Models\Island::query()
        ->whereNotNull('map_x')
        ->whereNotNull('map_y')
        ->orderBy('name')
        ->get(['name', 'type', 'map_x', 'map_y']);
@endphp

@once
    <style>
        .hb-admin-picker {
            border: 1px solid rgba(148, 163, 184, 0.18);
            background:
                radial-gradient(circle at 18% 18%, rgba(139, 92, 246, 0.18), transparent 24%),
                radial-gradient(circle at 82% 76%, rgba(94, 234, 212, 0.08), transparent 20%),
                linear-gradient(180deg, #0b0b0d, #050507);
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.85);
            overflow: hidden;
            position: relative;
        }

        .hb-admin-picker::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.35;
            pointer-events: none;
        }

        .hb-admin-picker__island {
            position: absolute;
            inset: 11% 14% 13% 16%;
            background: rgba(17, 18, 20, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.16);
            clip-path: polygon(9% 10%, 27% 4%, 46% 10%, 66% 3%, 88% 16%, 94% 31%, 92% 48%, 99% 63%, 85% 82%, 63% 93%, 44% 86%, 24% 94%, 7% 82%, 3% 63%, 11% 45%, 4% 26%);
            box-shadow: inset 0 0 48px rgba(0, 0, 0, 0.9), 0 16px 48px rgba(0, 0, 0, 0.35);
            pointer-events: none;
        }

        .hb-admin-picker__label {
            position: absolute;
            transform: translate(-50%, -50%);
            pointer-events: none;
            color: rgba(226, 232, 240, 0.5);
            font-size: 0.72rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            text-shadow: 0 0 18px rgba(0, 0, 0, 0.9);
            white-space: nowrap;
        }

        .hb-admin-picker__label--shore {
            color: rgba(192, 132, 252, 0.5);
        }

        .hb-admin-picker__marker {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 1.05rem;
            height: 1.05rem;
            border-radius: 9999px;
            border: 1px solid rgba(216, 180, 254, 0.75);
            background: radial-gradient(circle, rgba(244, 114, 182, 0.95), rgba(109, 40, 217, 0.9));
            box-shadow: 0 0 0 10px rgba(139, 92, 246, 0.15), 0 0 22px rgba(139, 92, 246, 0.5);
            cursor: grab;
            z-index: 2;
        }

        .hb-admin-picker__marker:active {
            cursor: grabbing;
        }
    </style>

    <script>
        (() => {
            const bootPicker = () => {
                document.querySelectorAll('[data-hb-admin-picker]').forEach((root) => {
                    if (root.dataset.ready === '1') {
                        return;
                    }

                    root.dataset.ready = '1';

                    const form = root.closest('form') ?? document;
                    const xInput = form.querySelector('[data-horror-map-x]');
                    const yInput = form.querySelector('[data-horror-map-y]');
                    const canvas = root.querySelector('[data-hb-admin-picker-canvas]');
                    const marker = root.querySelector('[data-hb-admin-picker-marker]');

                    if (! xInput || ! yInput || ! canvas || ! marker) {
                        return;
                    }

                    const clamp = (value) => Math.min(94, Math.max(6, value));

                    const dispatch = (input) => {
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    };

                    const setPosition = (x, y) => {
                        const nextX = clamp(x);
                        const nextY = clamp(y);

                        marker.style.left = `${nextX}%`;
                        marker.style.top = `${nextY}%`;
                        xInput.value = nextX.toFixed(2);
                        yInput.value = nextY.toFixed(2);
                        dispatch(xInput);
                        dispatch(yInput);
                    };

                    const pointFromEvent = (event) => {
                        const rect = canvas.getBoundingClientRect();

                        return {
                            x: ((event.clientX - rect.left) / rect.width) * 100,
                            y: ((event.clientY - rect.top) / rect.height) * 100,
                        };
                    };

                    const readInput = (input, fallback) => {
                        const value = Number.parseFloat(input.value);
                        return Number.isFinite(value) ? value : fallback;
                    };

                    let dragging = false;

                    marker.addEventListener('pointerdown', (event) => {
                        dragging = true;
                        marker.setPointerCapture(event.pointerId);
                        event.preventDefault();
                    });

                    marker.addEventListener('pointermove', (event) => {
                        if (! dragging) {
                            return;
                        }

                        const point = pointFromEvent(event);
                        setPosition(point.x, point.y);
                    });

                    marker.addEventListener('pointerup', () => {
                        dragging = false;
                    });

                    marker.addEventListener('pointercancel', () => {
                        dragging = false;
                    });

                    canvas.addEventListener('click', (event) => {
                        if (event.target === marker) {
                            return;
                        }

                        const point = pointFromEvent(event);
                        setPosition(point.x, point.y);
                    });

                    setPosition(readInput(xInput, 50), readInput(yInput, 50));
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bootPicker);
            } else {
                bootPicker();
            }

            document.addEventListener('livewire:navigated', bootPicker);
            document.addEventListener('livewire:initialized', bootPicker);
        })();
    </script>
@endonce

<div class="space-y-4" data-hb-admin-picker>
    <div>
        <p class="text-sm font-medium text-white">Public Map Placement</p>
        <p class="mt-1 text-xs text-gray-400">Click or drag the marker to place this record on the fictional customer-facing island map.</p>
    </div>

    <div class="hb-admin-picker aspect-[16/9]" data-hb-admin-picker-canvas>
        <div class="hb-admin-picker__island"></div>

        @foreach ($zones as $zone)
            <span
                class="hb-admin-picker__label {{ str_contains($zone->type, 'Picnic') ? 'hb-admin-picker__label--shore' : '' }}"
                style="left: {{ $zone->map_x }}%; top: {{ $zone->map_y }}%;"
            >
                {{ $zone->name }}
            </span>
        @endforeach

        <button type="button" class="hb-admin-picker__marker" data-hb-admin-picker-marker aria-label="Drag marker"></button>
    </div>
</div>
