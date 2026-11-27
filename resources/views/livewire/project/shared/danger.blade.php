<div>
    @php
        $resourceLabel = match ($resource?->type()) {
            'application' => __('project.application'),
            'service' => __('common.service'),
            'service-application' => __('service application'),
            'service-database' => __('service database'),
            default => __('resource'),
        };
    @endphp
    @if ($resource && !data_get($resource, 'destination.server')?->isFunctional())
        <x-callout type="warning" :title="__('onboarding.server_not_reachable')" class="mb-4">
            {{ __('Coolify cannot stop or remove Docker resources on this server. Deleting removes this :label from Coolify only. Its containers, volumes, networks, and configuration files remain on the server and can start again when the server is back.', ['label' => $resourceLabel]) }}
        </x-callout>
    @endif
    <x-application.settings-section id="danger-zone-section" :title="__('menu.danger_zone')"
        :helper="__('Destructive resource actions cannot be undone.')">
        @if (in_array($resource?->type(), ['standalone-redis', 'standalone-keydb', 'standalone-dragonfly']))
            <x-danger-zone :title="__('Flush all data')" class="mb-4">
                <p> {{ __('Permanently remove every key from') }} <strong class="font-semibold text-black dark:text-fg">{{ $resourceName }}</strong>{{ __('. The database keeps running.') }} </p>
                <x-slot:action>
                    <x-modal-confirmation :title="__('Flush all data?')" :buttonTitle="__('Flush all data')" isErrorButton
                        submitAction="flush" :actions="[__('Permanently remove every key from every logical database.')]"
                        confirmationText="{{ $resourceName }}"
                        :confirmationLabel="__('Enter the database name to confirm flushing all data')"
                        :shortConfirmationLabel="__('database.database_name_short')" :disabled="auth()->user()->cannot('manage', $resource)"
                        :disabledTooltip="__('You do not have permission to flush this database.')" />
                </x-slot:action>
            </x-danger-zone>
        @endif
        <x-danger-zone :title="__('Delete :label', ['label' => $resourceLabel])">
                    <p> {{ __('button.permanently_delete') }} <strong class="font-semibold text-black dark:text-fg">{{ $resourceName }}</strong>{{ __(', stop its containers, and remove the selected Docker resources and configuration.') }} </p>
                    <ul class="space-y-1 text-xs">
                        <li>• {{ __('Active deployments will be stopped.') }}</li>
                        <li>• {{ __('Selected volumes and stored data may be permanently removed.') }}</li>
                        <li>• {{ __('This :label cannot be restored from Coolify after deletion.', ['label' => $resourceLabel]) }}</li>
                    </ul>
                <x-slot:action>
                    @if ($canDelete)
                        <x-modal-confirmation :title="__('Delete :label?', ['label' => $resourceLabel])"
                            :buttonTitle="__('Delete :label', ['label' => $resourceLabel])"
                            isErrorButton submitAction="delete" :checkboxes="$checkboxes"
                            :actions="[__('Permanently delete this resource and its selected Docker resources.')]"
                            confirmationText="{{ $resourceName }}"
                            :confirmationLabel="__('Enter the resource name to confirm permanent deletion')"
                            :shortConfirmationLabel="__('danger.resource_name')" />
                    @else
                        <x-forms.button isError disabled :tooltip="__('You do not have permission to delete this resource.')">
                            {{ __('Delete :label', ['label' => $resourceLabel]) }}
                        </x-forms.button>
                    @endif
                </x-slot:action>
        </x-danger-zone>

        @if (!$canDelete)
            <div class="mt-4">
                <x-callout type="danger" :title="__('Insufficient permissions')"> {{ __('Contact a team administrator if this resource must be deleted.') }} </x-callout>
            </div>
        @endif
    </x-application.settings-section>
</div>
