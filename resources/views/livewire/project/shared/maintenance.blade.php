<form wire:submit="submit">
    <x-unsaved-bar action="submit" targets="maintenancePage" />
    <x-application.settings-section id="maintenance-section" :title="__('Maintenance mode')"
        :helper="__('Show a maintenance page with a 503 status on all domains of this resource. The containers keep running, and the change applies without a redeploy.')">
        @if ($this->isSupported)
            <x-slot:badges>
                {{-- The inline wrapper puts the badge text on the same line as the title text. --}}
                <span>
                    @if ($isMaintenanceEnabled)
                        <x-status-badge :label="__('On')" type="success" />
                    @else
                        <x-status-badge :label="__('Off')" type="neutral" />
                    @endif
                </span>
            </x-slot:badges>
            @can('update', $resource)
                <x-slot:actions>
                    <x-modal-confirmation wire:key="maintenance-toggle-{{ $isMaintenanceEnabled ? 'on' : 'off' }}"
                        :ignoreWire="false" submitAction="toggleMaintenance" :confirmWithText="false"
                        :confirmWithPassword="false" :isErrorButton="! $isMaintenanceEnabled"
                        :title="$isMaintenanceEnabled ? __('Disable maintenance mode?') : __('Enable maintenance mode?')"
                        :buttonTitle="$isMaintenanceEnabled ? __('Disable maintenance') : __('Enable maintenance')"
                        :step2ButtonText="$isMaintenanceEnabled ? __('Disable maintenance') : __('Enable maintenance')"
                        :warningMessage="$isMaintenanceEnabled
                            ? __('Visitors can reach this resource again right away.')
                            : __('Visitors cannot reach this resource until you disable maintenance mode.')"
                        :actions="$isMaintenanceEnabled
                            ? [__('All domains of this resource serve the resource again.')]
                            : [__('All domains of this resource show the maintenance page with a 503 status.'), __('The containers keep running.')]" />
                </x-slot:actions>
            @endcan
            <div class="flex flex-col gap-4">
                <x-forms.textarea canGate="update" :canResource="$resource" id="maintenancePage" rows="10" monospace
                    :label="__('Custom maintenance page (HTML)')"
                    :placeholder="__('Leave empty to use the Coolify default maintenance page.')"
                    :helper="__('One self-contained HTML file (max :bytes KB) with inline CSS and images.', ['bytes' => \App\Models\Server::PROXY_ERROR_PAGE_MAX_BYTES / 1024])" />
                @can('update', $resource)
                    <div class="flex gap-2">
                        <x-forms.button type="button" wire:click="resetMaintenancePage">{{ __('Reset to default') }}</x-forms.button>
                    </div>
                @endcan
            </div>
        @else
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Maintenance mode needs a Traefik or Caddy proxy on a server that is not part of a Docker Swarm.') }}</p>
        @endif
    </x-application.settings-section>
</form>
