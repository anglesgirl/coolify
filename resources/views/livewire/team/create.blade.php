<form class="application-settings-form flex w-full flex-col gap-4" wire:submit="submit">
    <x-forms.input id="name" label="{{ __('scheduled_task.name_label') }}" required />
    <x-forms.input id="description" label="{{ __('input.description') }}" />
    <div class="flex justify-end">
        <x-forms.button type="submit"
            defaultClass="button button-highlighted">
            {{ __('Create team') }}
        </x-forms.button>
    </div>
</form>
