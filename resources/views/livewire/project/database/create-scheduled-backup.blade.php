<form class="application-settings-form flex w-full flex-col gap-4" wire:submit="submit">
    @if ($service)
        <x-forms.listbox id="selectedDatabaseUuid" label="{{ __('database.database') }}" :options="$databaseOptions"
            empty-text="{{ __('No databases in this service support scheduled backups.') }}" required />
        @error('selectedDatabaseUuid')
            <p class="text-xs text-error">{{ $message }}</p>
        @enderror
    @endif
    <x-forms.input placeholder="0 0 * * * or daily" id="frequency"
        helper="{{ __('scheduled_task.frequency_helper') }}" label="{{ __('scheduled_task.frequency_label') }}"
        required />
    <div class="flex justify-end border-t border-neutral-200 pt-4 dark:border-white/[0.08]">
        <x-forms.button type="submit" @click="modalOpen=false" isHighlighted>
            {{ __('Add schedule') }}
        </x-forms.button>
    </div>
</form>
