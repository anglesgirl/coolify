<x-modal-confirmation title="{{ __('modal.confirm_environment_deletion') }}" buttonTitle="{{ __('button.delete') }}" isErrorButton
    submitAction="delete" :actions="[__('This will delete the selected environment.')]"
    confirmationLabel="{{ __('project.confirm_delete_environment_label') }}"
    shortConfirmationLabel="{{ __('project.environment_name') }}" confirmationText="{{ $environmentName }}" :confirmWithPassword="false"
    step2ButtonText="{{ __('button.permanently_delete') }}" />
