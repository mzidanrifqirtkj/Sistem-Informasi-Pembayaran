@props(['status', 'type'])

@if ($status)
    <div
        {{ $attributes->merge(['class' => 'font-medium text-sm ' . ($type === 'error' ? 'text-red-600' : 'text-green-600')]) }}>
        {{ $status }}
    </div>
@endif
