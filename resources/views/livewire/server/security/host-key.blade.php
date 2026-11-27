<div class="application-settings-form flex w-full flex-col gap-6">
    @if (count($pendingFingerprints) > 0)
        <x-callout type="danger" :title="__('The SSH host key of this server changed')">
            @foreach ($pendingFingerprints as $fingerprint)
                <code class="block break-all">{{ $fingerprint }}</code>
            @endforeach
            <p class="mt-2">{{ $mode === 'enforce' ? __('Coolify does not connect until you trust or reset the key.') : __('Coolify still connects (warn mode).') }}
                {{ __('Expected after a reinstall. Otherwise, compare the key on the server before you trust it.') }}</p>
        </x-callout>
    @endif

    <x-application.settings-section id="server-host-key-section" :title="__('SSH host key')"
        :helper="__('Coolify trusts the first host key of the server. Later, a different key is reported (warn mode, the default) or refused (enforce mode). The setting below sets enforce mode for this server only.<br><br>An instance admin sets the mode for all servers with <code>SSH_HOST_KEY_CHECKING=enforce</code> (or <code>warn</code>, <code>off</code>) in <code>/data/coolify/source/.env</code>, then restarts Coolify.')">
        <x-slot:badges>
            @if (count($pendingFingerprints) > 0)
                <x-status-badge :status="__('Key changed')" type="error" />
            @elseif ($mode === 'off')
                <x-status-badge :status="__('Check off')" type="warning" />
            @elseif (count($fingerprints) > 0)
                <x-status-badge :status="__('Trusted, :mode mode', ['mode' => $mode])" type="success" />
            @else
                <x-status-badge :status="__('No key yet')" type="neutral" />
            @endif
        </x-slot:badges>
        <x-slot:actions>
            @can('update', $server)
                <div class="flex flex-wrap items-center gap-2"
                    wire:key="host-key-actions-{{ $server->settings->ssh_host_key_pending ? hash('sha256', $server->settings->ssh_host_key_pending) : 'none' }}-{{ $server->settings->is_ssh_host_key_check_disabled ? 'off' : 'on' }}">
                    @if (count($pendingFingerprints) > 0)
                        <x-modal-confirmation :title="__('Trust the new host key?')" temporaryDisableTwoStepConfirmation
                            :buttonTitle="__('Trust new key')"
                            submitAction="trustPendingKey('{{ hash('sha256', $server->settings->ssh_host_key_pending) }}')"
                            :actions="[
                                __('Coolify trusts the new host key and stops trusting the old key.'),
                                __('Only do this after you compared the new key with the key on the server.'),
                            ]" confirmationText="{{ $server->name }}"
                            :shortConfirmationLabel="__('server.server_name')" :step3ButtonText="__('Trust New Key')"
                            isHighlightedButton />
                    @endif
                    <x-modal-confirmation :title="__('Reset the host key?')" temporaryDisableTwoStepConfirmation
                        :buttonTitle="__('common.reset')" submitAction="resetHostKey" :actions="[
                            __('Coolify removes the trusted key and the new key.'),
                            __('Coolify trusts the key that the server sends on the next connection.'),
                        ]" confirmationText="{{ $server->name }}" :shortConfirmationLabel="__('server.server_name')"
                        :step3ButtonText="__('Reset Host Key')" isErrorButton />
                    <x-modal-confirmation
                        :title="$server->settings->is_ssh_host_key_check_disabled ? __('Enable the host key check?') : __('Disable the host key check?')"
                        temporaryDisableTwoStepConfirmation
                        :buttonTitle="$server->settings->is_ssh_host_key_check_disabled ? __('Enable check') : __('Disable check')"
                        submitAction="setHostKeyCheckDisabled({{ $server->settings->is_ssh_host_key_check_disabled ? 0 : 1 }})"
                        :actions="$server->settings->is_ssh_host_key_check_disabled
                            ? [__('Coolify checks the host key of this server again.')]
                            : [
                                __('Coolify does not check the host key of this server.'),
                                __('Use this only for a server that changes its host key often, for example a load balancer address or a server that you rebuild often.'),
                            ]" confirmationText="{{ $server->name }}"
                        :shortConfirmationLabel="__('server.server_name')" :step3ButtonText="__('common.confirm')"
                        :isErrorButton="!$server->settings->is_ssh_host_key_check_disabled" />
                </div>
            @endcan
        </x-slot:actions>

        <div class="flex flex-col gap-3 text-sm">
            @if ($mode === 'off')
                <x-callout type="warning" :title="__('The host key check is off')">
                    {{ $server->settings->is_ssh_host_key_check_disabled ? __('Disabled for this server.') : __('Disabled for this instance (SSH_HOST_KEY_CHECKING=off).') }}
                </x-callout>
            @endif

            <div class="flex min-w-0 items-start gap-3">
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-neutral-100 text-neutral-500 dark:bg-white/[0.06] dark:text-fg-dim">
                    <x-reicon name="keys" class="size-4" />
                </div>
                <div class="min-w-0">
                    @forelse ($fingerprints as $fingerprint)
                        <code class="block break-all text-xs">{{ $fingerprint }}</code>
                    @empty
                        <p class="font-medium text-neutral-950 dark:text-fg">{{ __('No key yet') }}</p>
                    @endforelse
                    <p class="mt-1 text-xs text-neutral-500 dark:text-fg-dim">
                        @if (count($fingerprints) > 0)
                            {{ match ($server->settings->ssh_host_key_source) {
                                'tofu' => __('Saved on the first connection'),
                                'upgrade' => __('Saved on the first connection after the Coolify upgrade'),
                                'transfer' => __('Moved with the server transfer'),
                                'manual' => __('Saved by a user'),
                                default => __('Saved'),
                            } }}
                            @if ($server->settings->ssh_host_key_trusted_at)
                                &middot; {{ __('Trusted :time UTC', ['time' => $server->settings->ssh_host_key_trusted_at->toDateTimeString()]) }}
                            @endif
                            &middot;
                        @else
                            {{ __('Saved on the next connection') }} &middot;
                        @endif
                        {{ __('Mode: :mode', ['mode' => $mode]) }}{{ $mode === 'enforce' && $instanceMode !== 'enforce' ? ' '.__('(set for this server)') : '' }}
                    </p>
                </div>
            </div>

            {{-- The choice only changes something when the instance warns; an instance "enforce" or "off" decides alone. --}}
            @if ($instanceMode !== 'enforce' && $instanceMode !== 'off' && ! $server->settings->is_ssh_host_key_check_disabled)
                <div class="max-w-sm">
                    <x-forms.listbox id="checkMode" :label="__('When the host key changes')" onChange="setHostKeyCheckMode"
                        :onChangeArgs="[]" canGate="update" :canResource="$server"
                        :helper="__('Warn: Coolify connects and reports the new key. Enforce: Coolify refuses to connect until you trust or reset the new key, so deployments, backups, and checks on this server stop. A reinstall of the server also changes the key.')"
                        :options="[
                            ['value' => 'warn', 'label' => __('Warn and connect')],
                            ['value' => 'enforce', 'label' => __('Refuse to connect (enforce)')],
                        ]" />
                </div>
            @endif
        </div>
    </x-application.settings-section>
</div>
