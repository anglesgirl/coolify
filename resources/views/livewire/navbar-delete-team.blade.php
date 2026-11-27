<div class="w-full min-w-0">
    @if (auth()->user()->roleInTeam(currentTeam()->id) === 'owner')
    <x-modal-confirmation title="{{ __('teams.confirm_delete') }}" buttonFullWidth isErrorButton submitAction="delete"
        :actions="[
            __('The current Team will be permanently deleted.'),
        ]" confirmationText="{{ $team }}"
        confirmationLabel="{{ __('teams.confirm_label') }}"
        shortConfirmationLabel="{{ __('teams.team_name') }}">
        <x-slot:trigger>
            <button type="button" title="{{ __('teams.delete_team') }}" aria-label="{{ __('teams.delete_team') }}"
                class="menu-item justify-start text-left !text-error hover:!bg-error/10 dark:!text-error dark:hover:!bg-error/15">
                <x-reicon name="trash" class="menu-item-icon" />
                <span class="menu-item-label sidebar-collapsed-label text-left">{{ __('teams.delete_team') }}</span>
            </button>
        </x-slot:trigger>
    </x-modal-confirmation>
    @endif
</div>
