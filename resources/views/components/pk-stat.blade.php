@props([
    'title' => '',
    'value' => '0',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'p-5 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-between']) }}>
    <div>
        @if ($title)
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">{{ $title }}</p>
        @endif
        <h3 class="text-2xl font-bold text-gray-800">{{ $value }}</h3>
    </div>

    @if ($icon)
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
            {!! $icon !!}
        </div>
    @elseif (isset($slot) && $slot->isNotEmpty())
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
            {{ $slot }}
        </div>
    @endif
</div>