<x-modal-confirmation title="{{ __('modal.confirm_project_deletion') }}" buttonTitle="{{ __('modal.delete_project') }}" isErrorButton submitAction="delete"
    :actions="[
        __('This will delete the selected project'),
        __('All Environments inside the project will be deleted as well.'),
    ]" confirmationLabel="{{ __('project.confirm_delete_label') }}"
    shortConfirmationLabel="{{ __('project.project_name') }}" confirmationText="{{ $projectName }}" :confirmWithPassword="false"
    step2ButtonText="{{ __('button.permanently_delete') }}" />
