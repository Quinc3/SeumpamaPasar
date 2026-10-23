@props(['icon' => 'information-circle', 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4']) }}>
    <div class="rounded-full bg-surface-100 p-3 mb-4">
        <x-icon :name="$icon" size="xl" class="text-surface-400" />
    </div>
    <h3 class="text-base font-semibold text-surface-900">{{ $title }}</h3>
    @if ($description)
        <p class="text-sm text-surface-600 mt-1 max-w-sm">{{ $description }}</p>
    @endif
    @if (isset($action))
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>