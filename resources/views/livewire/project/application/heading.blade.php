<nav wire:poll.10000ms="checkStatus" class="w-full max-w-none" x-data
    x-init="@js($loadComposeOnOpen) && $wire.loadComposeFile(true)">
    @teleport('#resource-star-slot-mobile')
        <livewire:star-toggle :resource="$application" :wire:key="'star-mobile-'.$application->getMorphClass().'-'.$application->uuid" />
    @endteleport
    <div>
        @if ($application->build_pack === 'nixpacks')
            <x-callout type="warning" :title="__('Nixpacks is deprecated')" class="mb-4">
                {{ config('deprecations.nixpacks') }}
                <a class="underline" href="{{ route('project.application.configuration', $parameters) }}" {{ wireNavigate() }}>
                    {{ __('Switch to Railpack in General settings.') }}
                </a>
            </x-callout>
        @endif

        {{-- Below xl: the title and the resource actions share one row. --}}
        <div class="mb-3 flex w-full min-w-0 items-start gap-3 xl:hidden">
            <div class="min-w-0 flex-1">
                <div class="flex min-w-0 flex-col items-start gap-2">
                    <h1 class="min-w-0 max-w-full truncate text-[24px]! leading-7! font-semibold! tracking-tight! text-black dark:text-fg">
                        {{ $application->name }}
                    </h1>
                    <div class="relative flex w-full min-w-0 items-center gap-2">
                        <x-status-summary :status="$application->status"
                            :healthcheck-url="route('project.application.healthcheck', $parameters)" />
                        <x-applications.links :application="$application" compact />
                        @if ($this->runningDeploymentUrl)
                            <x-deploying-indicator :href="$this->runningDeploymentUrl" />
                        @endif
                    </div>
                    <div class="flex w-full flex-wrap gap-1">
                        <x-application.restart-limit-warning :application="$application" />
                        @if ($this->imageScanGate)
                            <x-image-scan.gate-status :result="$this->imageScanGate['result']" :action="$this->imageScanGate['action']"
                                :vulnerabilities-url="$this->imageScanGate['vulnerabilities_url']"
                                :deployment-url="$this->imageScanGate['deployment_url']" />
                        @endif
                    </div>
                </div>
            </div>

            <div class="shrink-0">
                @if ($this->needsComposeFile())
                    <div class="flex items-center gap-1">
                        @can('update', $application)
                            <x-forms.button wire:click="loadComposeFile">{{ __('Load compose') }}</x-forms.button>
                        @endcan
                        @can('deploy', $application)
                            <x-forms.button id="application-mobile-actions" disabled default-class="split-action-main rounded-md!"
                                :tooltip="__('Load the Compose file before you deploy.')">
                                <x-reicon name="play-circle" class="size-3.5" />
                                {{ __('destination.deploy') }}
                            </x-forms.button>
                        @endcan
                    </div>
                @else
                    @can('deploy', $application)
                        <x-split-action id="application-mobile-actions">
                            @if (str($application->status)->startsWith('exited'))
                                <x-slot:main wire:click="deploy">
                                    <x-reicon name="play-circle" class="size-3.5" />
                                    {{ $application->stoppedAfterRestartLimit() ? __('Retry deployment') : __('destination.deploy') }}
                                </x-slot:main>
                                @if (!$application->destination?->server?->isSwarm())
                                    <button type="button" class="listbox-option justify-start! gap-2.5!" wire:click="deploy(true)"
                                        @click="open = false" role="menuitem">
                                        <x-reicon name="refresh" class="size-3.5 opacity-70" />
                                        {{ $application->stoppedAfterRestartLimit() ? __('Retry deployment (without cache)') : __('Deploy (without cache)') }}
                                    </button>
                                @endif
                                @if ($application->container_present !== false)
                                    <button type="button" class="listbox-option justify-start! gap-2.5!"
                                        @click="open = false; document.getElementById('application-mobile-stop-trigger')?.click()" role="menuitem">
                                        <x-reicon name="stop-circle" class="size-3.5 text-error" />
                                        {{ __('Remove container') }}
                                    </button>
                                @endif
                            @else
                                @if ($application->destination?->server?->isSwarm())
                                    @if ($application->build_pack !== 'dockercompose')
                                        <x-slot:main wire:click="deploy">
                                            <x-reicon name="refresh" class="size-3.5" />
                                            {{ __('application.update_service') }}
                                        </x-slot:main>
                                    @else
                                        <x-slot:main @click="document.getElementById('application-mobile-stop-trigger')?.click()">
                                            <x-reicon name="stop-circle" class="size-3.5" />
                                            {{ __('destination.stop') }}
                                        </x-slot:main>
                                    @endif
                                @else
                                    <x-slot:main wire:click="deploy">
                                        <x-reicon name="refresh" class="size-3.5" />
                                        {{ __('application.redeploy') }}
                                    </x-slot:main>
                                    <button type="button" class="listbox-option justify-start! gap-2.5!"
                                        wire:click="{{ str($application->status)->startsWith('running') ? 'force_deploy_without_cache' : 'deploy(true)' }}"
                                        @click="open = false" role="menuitem">
                                        <x-reicon name="refresh" class="size-3.5 opacity-70" />
                                        {{ __('Redeploy (without cache)') }}
                                    </button>
                                    @if ($application->build_pack !== 'dockercompose')
                                        <button type="button" class="listbox-option justify-start! gap-2.5!"
                                            @click="open = false; document.getElementById('application-mobile-restart-trigger')?.click()"
                                            role="menuitem">
                                            <x-reicon name="restart" class="size-3.5 opacity-70" />
                                            {{ __('database.restart') }}
                                        </button>
                                    @endif
                                @endif
                                @unless ($application->destination?->server?->isSwarm() && $application->build_pack === 'dockercompose')
                                    <button type="button" class="listbox-option justify-start! gap-2.5!"
                                        @click="open = false; document.getElementById('application-mobile-stop-trigger')?.click()"
                                        role="menuitem">
                                        <x-reicon name="stop-circle" class="size-3.5 text-error" />
                                        {{ __('destination.stop') }}
                                    </button>
                                @endunless
                            @endif
                        </x-split-action>
                    @endcan
                @endif
                <div class="hidden" aria-hidden="true">
                    <x-modal-confirmation
                        canGate="deploy" :canResource="$application"
                        title="{{ str($application->status)->startsWith('exited') ? __('Confirm Container Removal?') : __('application.confirm_stop_title') }}"
                        buttonTitle="{{ str($application->status)->startsWith('exited') ? __('Remove container') : __('destination.stop') }}"
                        submitAction="stop" :checkboxes="$checkboxes" :actions="[
                            str($application->status)->startsWith('exited') ? __('The exited application container will be removed.') : __('application.confirm_stop_action_1'),
                            str($application->status)->startsWith('exited') ? __('Anonymous volumes may become eligible for Docker cleanup.') : __('application.confirm_stop_action_2'),
                        ]" :confirmWithText="false" :confirmWithPassword="false"
                        step1ButtonText="{{ __('common.continue') }}" step2ButtonText="{{ __('common.confirm') }}">
                        <x-slot:trigger>
                            <button id="application-mobile-stop-trigger" type="button">{{ __('destination.stop') }}</button>
                        </x-slot:trigger>
                    </x-modal-confirmation>
                    @php
                        $restartPlan = $this->restartPlan;
                        $restartReasons = collect($restartPlan['reasons']);
                        $restartReasonText = $restartReasons->take(3)->implode(', ')
                            . ($restartReasons->count() > 3 ? __(' and ') . ($restartReasons->count() - 3) . __(' more') : '');
                    @endphp
                    <x-modal-confirmation :title="__('Confirm Application Restart?')" :buttonTitle="__('database.restart')"
                        submitAction="restart" :ignoreWire="false"
                        :warningMessage="$restartPlan['builds']
                            ? __('This restart builds a new image, because a build setting changed: :reasons.', ['reasons' => e($restartReasonText)])
                            : __('This restart does not build. It recreates the container from the current image.')"
                        :actions="$restartPlan['builds']
                            ? [
                                __('Build a new image. A restart cannot reuse the current image, because the build output can change.'),
                                __('Recreate the container with the new image and the current runtime settings.'),
                            ]
                            : [
                                __('Recreate the container from the current image.'),
                                __('Apply the current runtime environment variables, ports, mounts, and labels.'),
                                __('Build only if the image is no longer on the server.'),
                            ]"
                        :confirmWithText="false" :confirmWithPassword="false"
                        :step2ButtonText="__('common.confirm')">
                        <x-slot:trigger>
                            <button id="application-mobile-restart-trigger" type="button">{{ __('database.restart') }}</button>
                        </x-slot:trigger>
                    </x-modal-confirmation>
                </div>
            </div>
        </div>

        @if ($this->imageScanGate)
            {{-- From xl the topbar shows the warning. Below xl the in-flow heading above repeats it. --}}
            @teleport('#configuration-warning-hud-slot')
                <div class="hidden items-center xl:flex">
                    <x-image-scan.gate-status :result="$this->imageScanGate['result']" :action="$this->imageScanGate['action']"
                        :vulnerabilities-url="$this->imageScanGate['vulnerabilities_url']"
                        :deployment-url="$this->imageScanGate['deployment_url']" />
                </div>
            @endteleport
        @endif

        {{-- Desktop actions live in the fixed top bar so they cannot overlap page content. --}}
        @teleport('#resource-action-hud-slot')
        <div class="hidden w-full items-center xl:flex xl:w-auto">
            <div
                class="resource-heading-navbar application-heading-actions flex w-full min-w-0 items-center justify-start gap-1 overflow-visible xl:w-auto xl:justify-end">
                <div class="resource-heading-actions flex shrink-0 items-center gap-0.5">
                    @if ($this->runningDeploymentUrl)
                        <x-deploying-indicator :href="$this->runningDeploymentUrl" class="mr-1" />
                    @endif
                    @if ($this->needsComposeFile())
                        @can('update', $application)
                            <x-forms.button wire:click="loadComposeFile">{{ __('Load compose') }}</x-forms.button>
                        @endcan
                        @can('deploy', $application)
                            <x-forms.button id="application-desktop-actions" disabled default-class="split-action-main rounded-md!"
                                :tooltip="__('Load the Compose file before you deploy.')">
                                <x-reicon name="play-circle" class="size-3.5" />
                                {{ __('destination.deploy') }}
                            </x-forms.button>
                        @endcan
                    @else
                        <div class="resource-heading-menus shrink-0">
                            <x-applications.links :application="$application" />
                        </div>
                        @can('deploy', $application)
                            <x-split-action id="application-desktop-actions">
                                @if (str($application->status)->startsWith('exited'))
                                    <x-slot:main wire:click="deploy">
                                        <x-reicon name="play-circle" class="size-3.5" />
                                        {{ $application->stoppedAfterRestartLimit() ? __('Retry deployment') : __('destination.deploy') }}
                                    </x-slot:main>
                                    @if (!$application->destination?->server?->isSwarm())
                                        <button type="button" class="listbox-option justify-start! gap-2.5!" wire:click="deploy(true)"
                                            @click="open = false" role="menuitem">
                                            <x-reicon name="refresh" class="size-3.5 opacity-70" />
                                            {{ $application->stoppedAfterRestartLimit() ? __('Retry deployment (without cache)') : __('Deploy (without cache)') }}
                                        </button>
                                    @endif
                                    @if ($application->container_present !== false)
                                        <button type="button" class="listbox-option justify-start! gap-2.5!"
                                            @click="open = false; document.getElementById('application-mobile-stop-trigger')?.click()" role="menuitem">
                                            <x-reicon name="stop-circle" class="size-3.5 text-error" />
                                            {{ __('Remove container') }}
                                        </button>
                                    @endif
                                @else
                                    @if ($application->destination?->server?->isSwarm())
                                        @if ($application->build_pack !== 'dockercompose')
                                            <x-slot:main wire:click="deploy">
                                                <x-reicon name="refresh" class="size-3.5" />
                                                {{ __('application.update_service') }}
                                            </x-slot:main>
                                        @else
                                            <x-slot:main @click="document.getElementById('application-mobile-stop-trigger')?.click()">
                                                <x-reicon name="stop-circle" class="size-3.5" />
                                                {{ __('destination.stop') }}
                                            </x-slot:main>
                                        @endif
                                    @else
                                        <x-slot:main wire:click="deploy">
                                            <x-reicon name="refresh" class="size-3.5" />
                                            {{ __('application.redeploy') }}
                                        </x-slot:main>
                                        <button type="button" class="listbox-option justify-start! gap-2.5!"
                                            wire:click="{{ str($application->status)->startsWith('running') ? 'force_deploy_without_cache' : 'deploy(true)' }}"
                                            @click="open = false" role="menuitem">
                                            <x-reicon name="refresh" class="size-3.5 opacity-70" />
                                            {{ __('Redeploy (without cache)') }}
                                        </button>
                                        @if ($application->build_pack !== 'dockercompose')
                                            <button type="button" class="listbox-option justify-start! gap-2.5!"
                                                @click="open = false; document.getElementById('application-mobile-restart-trigger')?.click()"
                                                role="menuitem">
                                                <x-reicon name="restart" class="size-3.5 opacity-70" />
                                                {{ __('database.restart') }}
                                            </button>
                                        @endif
                                    @endif
                                    @unless ($application->destination?->server?->isSwarm() && $application->build_pack === 'dockercompose')
                                        <button type="button" class="listbox-option justify-start! gap-2.5!"
                                            @click="open = false; document.getElementById('application-mobile-stop-trigger')?.click()"
                                            role="menuitem">
                                            <x-reicon name="stop-circle" class="size-3.5 text-error" />
                                            {{ __('destination.stop') }}
                                        </button>
                                    @endunless
                                @endif
                            </x-split-action>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
        @endteleport
    </div>
</nav>
