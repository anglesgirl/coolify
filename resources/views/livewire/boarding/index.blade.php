@php use App\Enums\ProxyTypes; @endphp
<x-slot:title>
    {{ __('onboarding.title') }}
    </x-slot>
    <section class="application-settings-form w-full py-6">
        <div class="flex w-full flex-col items-center space-y-6">
            @if ($currentState === 'welcome')
                <div class="w-full max-w-3xl">
                    <div class="mb-6 text-center">
                        <h1 class="text-2xl! font-semibold!">{{ __('onboarding.welcome_heading') }}</h1>
                        <p class="mt-1 text-[13px] text-neutral-500 dark:text-fg-dim">
                            {{ __('Connect your first server and start deploying in minutes.') }}
                        </p>
                    </div>

                    <x-application.settings-section title="{{ __('What you will set up') }}" flush>
                        <div class="divide-y divide-neutral-200 dark:divide-white/[0.07]">
                            @foreach ([
                                ['icon' => 'servers', 'title' => 'Server connection', 'description' => 'Connect through SSH to host your resources.'],
                                ['icon' => 'settings', 'title' => 'Docker environment', 'description' => 'Validate and configure the deployment runtime.'],
                                ['icon' => 'projects', 'title' => 'Project structure', 'description' => 'Create a project and its first environment.'],
                            ] as $onboardingItem)
                                <div class="flex min-h-14 items-center gap-3 px-4 py-3">
                                    <span
                                        class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-white/[0.08] dark:bg-white/[0.035] dark:text-fg-dim">
                                        <x-reicon :name="$onboardingItem['icon']" class="size-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-[13px] font-semibold">{{ $onboardingItem['title'] }}</span>
                                        <span class="mt-0.5 block text-[11px] text-neutral-500 dark:text-fg-faint">{{ $onboardingItem['description'] }}</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </x-application.settings-section>

                    <div class="mt-5 flex flex-col items-center gap-4">
                        <x-forms.button class="w-full justify-center sm:w-auto sm:min-w-36" wire:click="explanation"
                            isHighlighted>
                            {{ __('common.continue') }}
                        </x-forms.button>
                        <div
                            class="inline-flex flex-wrap items-center justify-center gap-0.5 rounded-lg border border-neutral-200 bg-neutral-50 p-0.5 dark:border-white/[0.08] dark:bg-white/[0.05]">
                            <button type="button" wire:click="skipBoarding"
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                                <x-reicon name="arrow-right" class="size-3.5 shrink-0" />
                                {{ __('Skip setup') }}
                            </button>
                            <x-modal-input title="{{ __('onboarding.need_help') }}">
                                <x-slot:content>
                                    <button type="button"
                                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                                        <x-reicon name="feedback" class="size-3.5 shrink-0" />
                                        {{ __('error.contact_support') }}
                                    </button>
                                </x-slot:content>
                                <livewire:help />
                            </x-modal-input>
                        </div>
                    </div>
                </div>
            @elseif ($currentState === 'explanation')
                <x-boarding-progress :currentStep="0" />
                <x-boarding-step title="{{ __('onboarding.platform_overview') }}">
                    <x-slot:question>
                        {{ __('Coolify automates deployment and infrastructure management on your own servers. Deploy applications from Git, manage databases, and monitor everything without vendor lock-in.') }}
                    </x-slot:question>
                    <x-slot:explanation>
                        <p>
                            <x-highlighted text="{{ __('onboarding.automation') }}" /> {{ __('onboarding.automation_desc') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.self_hosted') }}" /> {{ __('onboarding.self_hosted_desc') }}
                        </p>
                        <p>
                            <x-highlighted text="Monitoring & Alerts:" /> {{ __('onboarding.monitoring_alerts_desc') }}
                        </p>
                    </x-slot:explanation>
                    <x-slot:actions>
                        <x-forms.button class="w-full justify-center lg:w-auto" wire:click="explanation"
                            isHighlighted>
                            {{ __('common.continue') }}
                        </x-forms.button>
                    </x-slot:actions>
                </x-boarding-step>
            @elseif ($currentState === 'select-server-type')
                <x-boarding-progress :currentStep="1" />
                <x-boarding-step title="{{ __('onboarding.choose_server_type') }}">
                    <x-slot:question>
                        {{ __('onboarding.choose_server_type_desc') }}
                    </x-slot:question>
                    <x-slot:actions>
                        <div class="w-full space-y-6">
                            <section>
                                <h3 class="text-base font-semibold">{{ __('Add a server') }}</h3>
                                <p class="mb-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Use this machine or connect a server you already manage.') }}</p>
                                <div class="grid w-full grid-cols-1 gap-4 lg:grid-cols-2">
                            <button
                                class="group relative cursor-pointer min-h-36 rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]"
                                wire:target="setServerType('localhost')" wire:click="setServerType('localhost')">
                                <span role="button" tabindex="0" aria-label="{{ __('About this machine') }}"
                                    data-tooltip="{{ __('onboarding.localhost_explanation') }}"
                                    @click.stop @keydown.enter.stop @keydown.space.prevent.stop
                                    class="absolute top-3 right-3 flex size-6 items-center justify-center rounded-full border border-neutral-200 text-[11px] font-semibold text-neutral-500 hover:border-coollabs/35 hover:text-coollabs dark:border-white/[0.1] dark:text-fg-dim dark:hover:border-brand-light/30 dark:hover:text-brand-light">i</span>
                                <div class="flex flex-col gap-4 text-left">
                                    <svg class="size-10" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                                    </svg>
                                    <div>
                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('This machine') }}</h3>
                                        <p class="text-sm dark:text-neutral-400">
                                            {{ __('onboarding.this_machine_desc') }}
                                        </p>
                                    </div>
                                </div>
                            </button>



                            <button
                                class="group relative cursor-pointer min-h-36 rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]"
                                wire:target="setServerType('remote')" wire:click="setServerType('remote')">
                                <span role="button" tabindex="0" aria-label="{{ __('About remote servers') }}"
                                    data-tooltip="{{ __('Any SSH-accessible server, including cloud VPS, bare metal, and self-hosted infrastructure.') }}"
                                    @click.stop @keydown.enter.stop @keydown.space.prevent.stop
                                    class="absolute top-3 right-3 flex size-6 items-center justify-center rounded-full border border-neutral-200 text-[11px] font-semibold text-neutral-500 hover:border-coollabs/35 hover:text-coollabs dark:border-white/[0.1] dark:text-fg-dim dark:hover:border-brand-light/30 dark:hover:text-brand-light">i</span>
                                <div class="flex flex-col gap-4 text-left">
                                    <svg class="size-10" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                    </svg>
                                    <div>
                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('IP address or domain') }}</h3>
                                        <p class="text-sm dark:text-neutral-400">
                                            {{ __('Connect via SSH using a server IP address or domain.') }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                                </div>
                            </section>

                            @can('viewAny', App\Models\CloudProviderToken::class)
                                <section>
                                    <h3 class="text-base font-semibold">{{ __('Provision a server') }}</h3>
                                    <p class="mb-3 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create a server with a cloud provider.') }}</p>
                                    <div class="grid w-full grid-cols-1 gap-4 lg:grid-cols-2">
                                @if ($currentState === 'select-server-type')
                                    <x-modal-input title="{{ __('onboarding.connect_hetzner') }}" isFullWidth>
                                        <x-slot:content>
                                            <div
                                                class="group relative cursor-pointer flex h-full min-h-36 flex-col rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]">
                                                <div class="flex h-full flex-col gap-4 text-left">
                                                    <img src="{{ asset('svgs/hetzner.svg') }}" alt="{{ __('Hetzner') }}"
                                                        class="size-10 shrink-0">
                                                    <div class="min-h-0 flex-1">
                                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('onboarding.hetzner_cloud') }}</h3>
                                                        <p class="text-sm dark:text-neutral-400">
                                                            {{ __('onboarding.hetzner_cloud_desc') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:content>
                                        <livewire:server.new.by-hetzner :limit_reached="false" :from_onboarding="true" />
                                    </x-modal-input>
                                    <x-modal-input title="{{ __('Connect a Vultr Server') }}" isFullWidth>
                                        <x-slot:content>
                                            <div
                                                class="group relative cursor-pointer flex h-full min-h-36 flex-col rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]">
                                                <div class="flex h-full flex-col gap-4 text-left">
                                                    <img src="https://www.vultr.com/media/logo_ondark.svg" alt="{{ __('Vultr') }}"
                                                        class="h-10 w-28 shrink-0 object-contain object-left">
                                                    <div class="min-h-0 flex-1">
                                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('Vultr Cloud') }}</h3>
                                                        <p class="text-sm dark:text-neutral-400">
                                                            {{ __('Deploy servers directly from your Vultr account.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:content>
                                        <livewire:server.new.by-vultr :limit_reached="false" :from_onboarding="true" />
                                    </x-modal-input>
                                    <x-modal-input title="{{ __('Connect a DigitalOcean Server') }}" isFullWidth>
                                        <x-slot:content>
                                            <div
                                                class="group relative cursor-pointer flex h-full min-h-36 flex-col rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]">
                                                <div class="flex h-full flex-col gap-4 text-left">
                                                    <x-digital-ocean-icon class="size-10 shrink-0" />
                                                    <div class="min-h-0 flex-1">
                                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('DigitalOcean') }}</h3>
                                                        <p class="text-sm dark:text-neutral-400">
                                                            {{ __('Deploy servers directly from your DigitalOcean account.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:content>
                                        <livewire:server.new.by-digital-ocean :limit_reached="false" :from_onboarding="true" />
                                    </x-modal-input>
                                    <x-modal-input title="{{ __('Connect a Hostinger Server') }}" isFullWidth>
                                        <x-slot:content>
                                            <div
                                                class="group relative cursor-pointer flex h-full min-h-36 flex-col rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.025] dark:hover:border-white/[0.14]">
                                                <div class="flex h-full flex-col gap-4 text-left">
                                                    <x-hostinger-icon class="size-10 shrink-0 text-[#673de6]" />
                                                    <div class="min-h-0 flex-1">
                                                        <h3 class="mb-1 text-[14px] font-semibold">{{ __('Hostinger') }}</h3>
                                                        <p class="text-sm dark:text-neutral-400">
                                                            {{ __('Deploy servers directly from your Hostinger account.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </x-slot:content>
                                        <livewire:server.new.by-hostinger :limit_reached="false" :from_onboarding="true" />
                                    </x-modal-input>
                                @endif
                                    </div>
                                </section>
                            @endcan
                        </div>

                        @if (!$serverReachable)
                            <div class="mt-6 p-4 border border-error rounded-lg text-gray-800 dark:text-gray-200">
                                <h2 class="text-lg font-bold mb-2">{{ __('onboarding.server_not_reachable') }}</h2>
                                <p class="mb-4">{{ __('onboarding.check_connection_details') }}</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <x-forms.input placeholder="{{ __('onboarding.default_port') }}" label="{{ __('health_check.port') }}" id="remoteServerPort"
                                        wire:model="remoteServerPort" :value="$remoteServerPort" />
                                    <div>
                                        <x-forms.input placeholder="{{ __('onboarding.default_user') }}" label="{{ __('User') }}" id="remoteServerUser"
                                            wire:model="remoteServerUser" :value="$remoteServerUser" />
                                        <p class="text-xs mt-1">
                                            {{ __('onboarding.non_root_experimental') }}
                                            <a class="font-bold underline" target="_blank"
                                                href="https://coolify.io/docs/knowledge-base/server/non-root-user">{{ __('onboarding.docs') }}</a>
                                        </p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="mb-2">{{ __('onboarding.if_connection_correct') }}</p>
                                    <ul class="list-disc list-inside">
                                        <li>{{ __('The correct public key is in your') }} <code
                                                class="bg-red-200 dark:bg-red-900 px-1 rounded-sm">{{ __('~/.ssh/authorized_keys') }}</code>
                                            {{ __('file for the specified user') }}</li>
                                        <li>{{ __('onboarding.skip_boarding_hint') }}</li>
                                    </ul>
                                </div>

                                <p class="mb-4">
                                    {{ __('onboarding.more_help') }} <a target="_blank" class="underline font-semibold"
                                        href="https://coolify.io/docs/knowledge-base/server/openssh">{{ __('onboarding.documentation') }}</a>.
                                </p>

                                <x-forms.input readonly id="serverPublicKey" class="mb-4"
                                    label="{{ __('onboarding.current_public_key') }}"></x-forms.input>

                                <x-forms.button class="w-full justify-center" wire:click="saveAndValidateServer"
                                    isHighlighted>
                                    {{ __('Check again') }}
                                </x-forms.button>
                            </div>
                        @endif
                    </x-slot:actions>
                </x-boarding-step>
            @elseif ($currentState === 'private-key')
                <x-boarding-progress :currentStep="2" />
                <x-boarding-step title="{{ __('onboarding.ssh_authentication') }}">
                    <x-slot:question>
                        {{ __('onboarding.ssh_authentication_desc') }}
                    </x-slot:question>
                    <x-slot:actions>
                        @if ($privateKeys && $privateKeys->count() > 0)
                            @php
                                $privateKeyOptions = $privateKeys
                                    ->map(fn ($privateKey) => [
                                        'value' => $privateKey->id,
                                        'label' => $privateKey->name,
                                    ])
                                    ->values()
                                    ->all();
                            @endphp
                            <div class="w-full space-y-4">
                                <div
                                    class="rounded-[10px] border border-neutral-200 bg-neutral-50 p-4 dark:border-white/[0.08] dark:bg-white/[0.05]">
                                    <form wire:submit="selectExistingPrivateKey"
                                        class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                        <div class="min-w-0 flex-1">
                                            <x-forms.listbox id="selectedExistingPrivateKey"
                                                label="{{ __('Existing SSH key') }}" :options="$privateKeyOptions" :tooltip="false" />
                                        </div>
                                        <x-forms.button type="submit">{{ __('Use selected key') }}</x-forms.button>
                                    </form>
                                </div>
                                <div class="relative py-1">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t border-neutral-200 dark:border-white/[0.07]"></div>
                                    </div>
                                    <div class="relative flex justify-center">
                                        <span
                                            class="bg-[var(--coollabs-base)] px-2.5 text-[10px] font-semibold uppercase tracking-[0.08em] text-neutral-400 dark:text-fg-faint">
                                            {{ __('database.or') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="grid w-full grid-cols-1 gap-3 lg:grid-cols-2">
                            <button type="button"
                                class="group flex h-full min-h-28 items-start gap-3 rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]"
                                wire:target="setPrivateKey('own')" wire:click="setPrivateKey('own')">
                                <span
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-white/[0.08] dark:bg-white/[0.035] dark:text-fg-dim">
                                    <x-reicon name="keys" class="size-4" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[13px] font-semibold">{{ __('Use existing key') }}</span>
                                    <span class="mt-0.5 block text-[12px] leading-5 text-neutral-500 dark:text-fg-dim">
                                        {{ __('Paste a private key you already manage.') }}
                                    </span>
                                </span>
                            </button>
                            <button type="button"
                                class="group flex h-full min-h-28 items-start gap-3 rounded-[10px] border border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:-translate-y-px hover:border-neutral-300 hover:shadow-md dark:border-white/[0.08] dark:bg-white/[0.05] dark:hover:border-white/[0.14]"
                                wire:target="setPrivateKey('create')" wire:click="setPrivateKey('create')">
                                <span
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-white/[0.08] dark:bg-white/[0.035] dark:text-fg-dim">
                                    <x-reicon name="plus" class="size-4" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-[13px] font-semibold">{{ __('Generate new key') }}</span>
                                    <span class="mt-0.5 block text-[12px] leading-5 text-neutral-500 dark:text-fg-dim">
                                        {{ __('Create an ED25519 key pair in Coolify.') }}
                                    </span>
                                </span>
                            </button>
                        </div>
                    </x-slot:actions>
                    <x-slot:explanation>
                        <p>
                            <x-highlighted text="{{ __('onboarding.ssh_key_auth_label') }}" /> {{ __('onboarding.ssh_key_auth_desc') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.public_key_deployment_label') }}" /> {{ __('onboarding.public_key_deployment_desc') }}
                            <code
                                class="text-xs bg-coolgray-300 dark:bg-coolgray-400 px-1 py-0.5 rounded">{{ __('~/.ssh/authorized_keys') }}</code>
                            {{ __('onboarding.file') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.key_generation_label') }}" /> {{ __('onboarding.key_generation_desc') }}
                        </p>
                    </x-slot:explanation>
                </x-boarding-step>
            @elseif ($currentState === 'create-private-key')
                <x-boarding-progress :currentStep="2" />
                <x-boarding-step title="{{ __('onboarding.ssh_key_config') }}">
                    <x-slot:question>
                        {{ __('onboarding.ssh_key_config_desc') }}
                    </x-slot:question>
                    <x-slot:actions>
                        <form wire:submit='savePrivateKey' class="flex flex-col w-full gap-4">
                            <x-forms.input required placeholder="{{ __('onboarding.key_name_placeholder') }}" label="{{ __('onboarding.key_name') }}"
                                id="privateKeyName" />
                            <x-forms.input placeholder="{{ __('onboarding.key_description_placeholder') }}" label="{{ __('input.description') }}"
                                id="privateKeyDescription" />
                            @if ($privateKeyType === 'create')
                                <x-forms.textarea required readonly label="{{ __('Private Key') }}" id="privateKey" rows="8" />
                                <x-forms.textarea rows="7" readonly label="{{ __('onboarding.public_key') }}" id="publicKey" />
                            @else
                                <x-forms.textarea required placeholder="{{ __('-----BEGIN OPENSSH PRIVATE KEY-----') }}" label="{{ __('Private Key') }}"
                                    id="privateKey" rows="8" />
                            @endif
                            @if ($privateKeyType === 'create')
                                <div class="p-4 bg-warning/10 border border-warning rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="size-5 text-warning flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="font-bold text-warning mb-1">{{ __('onboarding.action_required') }}</p>
                                            <p class="text-sm dark:text-white text-black">
                                                {{ __('onboarding.action_required_desc') }}
                                                <code
                                                    class="text-xs bg-coolgray-300 dark:bg-coolgray-400 px-1 py-0.5 rounded">{{ __('~/.ssh/authorized_keys') }}</code>
                                                {{ __('onboarding.file') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <x-forms.button type="submit" class="w-full lg:w-auto">{{ __('onboarding.save_ssh_key') }}</x-forms.button>
                        </form>
                    </x-slot:actions>
                    <x-slot:explanation>
                        <p>
                            <x-highlighted text="{{ __('onboarding.key_storage_label') }}" /> {{ __('onboarding.key_storage_desc') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.public_key_distribution_label') }}" /> {{ __('onboarding.public_key_distribution_desc') }}
                            <code
                                class="text-xs bg-coolgray-300 dark:bg-coolgray-400 px-1 py-0.5 rounded">{{ __('~/.ssh/authorized_keys') }}</code>
                            {{ __('onboarding.on_target_server') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.key_format_label') }}" /> {{ __('Supports ED25519, ECDSA, and RSA (2048 bits or more) key types in OpenSSH format.') }}
                        </p>
                    </x-slot:explanation>
                </x-boarding-step>
            @elseif ($currentState === 'create-server')
                <x-boarding-progress :currentStep="2" />
                <x-boarding-step title="{{ __('onboarding.server_config') }}">
                    <x-slot:question>
                        {{ __('onboarding.server_config_desc') }}
                    </x-slot:question>
                    <x-slot:actions>
                        <form wire:submit='saveServer' class="flex flex-col w-full gap-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <x-forms.input required placeholder="{{ __('onboarding.server_name_placeholder') }}" label="{{ __('destination.server_name_label') }}"
                                    id="remoteServerName" wire:model="remoteServerName" />
                                <x-forms.input required placeholder="{{ __('onboarding.ip_address_placeholder') }}" label="{{ __('onboarding.ip_address') }}"
                                    id="remoteServerHost" wire:model="remoteServerHost" />
                            </div>
                            <x-forms.input placeholder="{{ __('onboarding.server_desc_placeholder') }}" label="{{ __('input.description') }}"
                                id="remoteServerDescription" wire:model="remoteServerDescription" />

                            <x-forms.collapsible title="{{ __('Advanced Settings') }}"
                                content-class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <x-forms.input placeholder="{{ __('onboarding.ssh_port_default') }}" label="{{ __('onboarding.ssh_port') }}" type="number"
                                    id="remoteServerPort" wire:model="remoteServerPort" />
                                <div>
                                    <x-forms.input placeholder="{{ __('onboarding.ssh_user_default') }}" label="{{ __('onboarding.ssh_user') }}" id="remoteServerUser"
                                        wire:model="remoteServerUser" />
                                </div>
                            </x-forms.collapsible>
                            <x-forms.button type="submit" class="w-full lg:w-auto">{{ __('onboarding.validate_connection') }}</x-forms.button>
                        </form>
                    </x-slot:actions>
                </x-boarding-step>
            @elseif ($currentState === 'validate-server')
                <x-boarding-progress :currentStep="2" />
                <x-boarding-step title="{{ __('onboarding.server_validation') }}">
                    <x-slot:question>
                        {{ __('Coolify will automatically install Docker') }} {{ $minDockerVersion }}{{ __('+ if not present.') }}
                    </x-slot:question>
                    <x-slot:actions>
                        <div class="flex w-full flex-col gap-4">
                            <div
                                class="overflow-hidden rounded-[10px] border border-neutral-200 dark:border-white/[0.08]">
                                <div
                                    class="border-b border-neutral-200 px-4 py-2.5 dark:border-white/[0.08]">
                                    <p
                                        class="text-[10px] font-semibold uppercase tracking-[0.08em] text-neutral-400 dark:text-fg-faint">
                                        {{ __('Validation checkpoints') }}
                                    </p>
                                </div>
                                <div class="divide-y divide-neutral-200 dark:divide-white/[0.07]">
                                    @foreach ([
                                        ['icon' => 'keys', 'title' => 'Test SSH connection', 'description' => 'Verify key-based authentication'],
                                        ['icon' => 'servers', 'title' => 'Check OS compatibility', 'description' => 'Verify supported Linux distribution'],
                                        ['icon' => 'layers', 'title' => 'Install Docker Engine', 'description' => 'Auto-install if version '.$minDockerVersion.'+ not found'],
                                        ['icon' => 'globe', 'title' => 'Configure network', 'description' => 'Set up Docker networks and proxy'],
                                    ] as $validationCheckpoint)
                                        <x-checkpoint-item :icon="$validationCheckpoint['icon']"
                                            :title="$validationCheckpoint['title']"
                                            :description="$validationCheckpoint['description']" status="idle" />
                                    @endforeach
                                </div>
                            </div>

                            @if ($prerequisiteInstallAttempts > 0)
                                <section class="application-settings-section">
                                    <header>
                                        <div class="flex items-center gap-2">
                                            <h3>{{ __('Installing prerequisites') }}</h3>
                                        </div>
                                    </header>
                                    <div class="application-settings-section-body">
                                        <livewire:activity-monitor header="Prerequisites installation logs"
                                            :showWaiting="false" />
                                    </div>
                                </section>
                            @endif

                            <x-process-dialog closeWithX size="xl">
                                <x-slot:title>{{ __('Server validation') }}</x-slot:title>
                                <x-slot:content>
                                    <livewire:server.validate-and-install :server="$this->createdServer" />
                                </x-slot:content>
                                <x-forms.button @click="processDialogOpen = true" class="w-full justify-center"
                                    wire:click.prevent="installServer" isHighlighted>
                                    {{ __('Start validation') }}
                                </x-forms.button>
                            </x-process-dialog>
                        </div>
                    </x-slot:actions>
                    <x-slot:explanation>
                        <p>
                            <x-highlighted text="{{ __('onboarding.automated_setup_label') }}" /> {{ __('onboarding.automated_setup_desc') }}
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.version_req_label') }}" /> {{ __('Minimum Docker Engine') }} {{ $minDockerVersion }}{{ __('.x required.') }}
                            <a target="_blank" class="underline hover:text-coollabs"
                                href="https://docs.docker.com/engine/install/#server">{{ __('onboarding.manual_install_guide') }}</a>
                        </p>
                        <p>
                            <x-highlighted text="{{ __('onboarding.system_config_label') }}" /> {{ __('onboarding.system_config_desc') }}
                        </p>
                    </x-slot:explanation>
                </x-boarding-step>
            @elseif ($currentState === 'create-project')
                <x-boarding-progress :currentStep="3" />
                <x-boarding-step title="{{ __('onboarding.project_setup') }}">
                    <x-slot:question>
                        @if ($projects && $projects->count() > 0)
                            {{ __('onboarding.project_setup_existing') }}
                        @else
                            {{ __('onboarding.project_setup_new') }}
                        @endif
                    </x-slot:question>
                    <x-slot:actions>
                        <div class="w-full space-y-4">
                            <x-forms.button class="w-full justify-center"
                                wire:click="createNewProject" isHighlighted>
                                {{ __('onboarding.create_first_project') }}
                            </x-forms.button>

                            @if ($projects && $projects->count() > 0)
                                @php
                                    $projectOptions = $projects
                                        ->map(fn ($project) => [
                                            'value' => $project->id,
                                            'label' => $project->name,
                                        ])
                                        ->values()
                                        ->all();
                                @endphp
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-neutral-300 dark:border-coolgray-400"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 text-neutral-500 dark:text-neutral-400">{{ __('onboarding.or_use_existing') }}</span>
                                    </div>
                                </div>
                                <form wire:submit="selectExistingProject"
                                    class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                    <div class="min-w-0 flex-1">
                                        <x-forms.listbox id="selectedProject" label="{{ __('Existing project') }}"
                                            :options="$projectOptions" />
                                    </div>
                                    <x-forms.button type="submit">{{ __('Use selected project') }}</x-forms.button>
                                </form>
                            @endif
                        </div>
                    </x-slot:actions>
                </x-boarding-step>
            @elseif ($currentState === 'create-resource')
                <x-boarding-progress :currentStep="3" />
                <div class="w-full max-w-3xl">
                    <div class="mb-6 text-center">
                        <div
                            class="mx-auto mb-4 flex size-12 items-center justify-center rounded-[10px] border border-emerald-500/25 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                            <x-reicon name="check-circle" class="size-6" />
                        </div>
                        <h1 class="text-2xl! font-semibold!">{{ __('Setup complete') }}</h1>
                        <p class="mt-1 text-[13px] text-neutral-500 dark:text-fg-dim">
                            {{ __('onboarding.setup_complete_desc') }}
                        </p>
                    </div>

                    <div
                        class="overflow-hidden rounded-[10px] border border-neutral-200 dark:border-white/[0.08]">
                        <div class="border-b border-neutral-200 px-4 py-2.5 dark:border-white/[0.08]">
                            <p
                                class="text-[10px] font-semibold uppercase tracking-[0.08em] text-neutral-400 dark:text-fg-faint">
                                {{ __('What\'s configured') }}
                            </p>
                        </div>
                        <div class="divide-y divide-neutral-200 dark:divide-white/[0.07]">
                            <x-checkpoint-item status="success" title="{{ __('logs.server_label') }} {{ $createdServer->name }}"
                                :description="$createdServer->ip" />
                            <x-checkpoint-item status="success" title="{{ __('Project:') }} {{ $createdProject->name }}"
                                description="{{ __('onboarding.production_ready') }}" />
                            <x-checkpoint-item status="success" title="{{ __('onboarding.docker_engine') }}"
                                description="{{ __('onboarding.installed_running') }}" />
                        </div>
                    </div>

                    <div class="mt-5 flex flex-col items-center gap-4">
                        <x-forms.button class="justify-center px-6" wire:click="showNewResource" isHighlighted>
                            {{ __('Deploy your first resource') }}
                        </x-forms.button>
                        <div
                            class="inline-flex flex-wrap items-center justify-center gap-0.5 rounded-lg border border-neutral-200 bg-neutral-50 p-0.5 dark:border-white/[0.08] dark:bg-white/[0.05]">
                            <button type="button" wire:click="skipBoarding"
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                                <x-reicon name="arrow-right" class="size-3.5 shrink-0" />
                                {{ __('Go to dashboard') }}
                            </button>
                            <x-modal-input title="{{ __('onboarding.need_help') }}">
                                <x-slot:content>
                                    <button type="button"
                                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                                        <x-reicon name="feedback" class="size-3.5 shrink-0" />
                                        {{ __('error.contact_support') }}
                                    </button>
                                </x-slot:content>
                                <livewire:help />
                            </x-modal-input>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if ($currentState !== 'welcome' && $currentState !== 'create-resource')
            <div class="mx-auto mt-6 flex w-full max-w-3xl flex-col items-center gap-3">
                <div
                    class="inline-flex flex-wrap items-center justify-center gap-0.5 rounded-lg border border-neutral-200 bg-neutral-50 p-0.5 dark:border-white/[0.08] dark:bg-white/[0.05]">
                    <button type="button" wire:click="skipBoarding"
                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                        <x-reicon name="arrow-right" class="size-3.5 shrink-0" />
                        {{ __('Skip setup') }}
                    </button>
                    <button type="button" wire:click="restartBoarding"
                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                        <x-reicon name="restart" class="size-3.5 shrink-0" />
                        {{ __('database.restart') }}
                    </button>
                    <x-modal-input title="{{ __('onboarding.need_help') }}">
                        <x-slot:content>
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[12px] font-medium text-neutral-500 transition-colors hover:bg-white hover:text-coollabs dark:text-fg-dim dark:hover:bg-white/[0.06] dark:hover:text-brand-light">
                                <x-reicon name="feedback" class="size-3.5 shrink-0" />
                                {{ __('error.contact_support') }}
                            </button>
                        </x-slot:content>
                        <livewire:help />
                    </x-modal-input>
                </div>
            </div>
        @endif
    </section>
