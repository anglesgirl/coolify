<button type="button" wire:click="toggle" wire:loading.attr="disabled"
    title="{{ $starred ? __('Remove from the dashboard') : __('Add to the dashboard') }}"
    aria-label="{{ $starred ? __('Unstar') : __('Star') }}" aria-pressed="{{ $starred ? 'true' : 'false' }}"
    @class([
        'flex size-7 shrink-0 items-center justify-center rounded-md transition-colors hover:bg-neutral-100 dark:hover:bg-white/[0.05]',
        'text-warning' => $starred,
        'text-neutral-400 hover:text-black dark:text-fg-faint dark:hover:text-fg' => ! $starred,
    ])>
    <x-reicon :name="$starred ? 'star-filled' : 'star'" class="size-4" />
</button>
