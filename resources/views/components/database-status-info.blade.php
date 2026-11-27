@props([
    'database',
    'label',
    'dbUrl' => null,
    'dbUrlPublic' => null,
    'supportsSsl' => true,
    'enableSsl' => false,
    'sslMode' => null,
    'sslModeOptions' => null,
    'sslModeHelper' => null,
    'certificateValidUntil' => null,
    'isExited' => false,
    'showPublicUrlPlaceholder' => false,
    'isPasswordHiddenForMember' => false,
])

@php
    $isCredentialLocked = ! $isPasswordHiddenForMember && method_exists($database, 'isCredentialLocked') && $database->isCredentialLocked();
    $urlHelper = 'If you change the user/password/port, this could be different. This is with the default values.';
@endphp

<div class="space-y-5">
    @if ($isPasswordHiddenForMember)
        <x-forms.input :label="$label . __('URL (internal)')" disabled value="{{ __('Hidden (only admins can view)') }}" />
        <x-forms.input :label="$label . __('URL (public)')" disabled value="{{ __('Hidden (only admins can view)') }}" />
    @elseif ($isCredentialLocked)
        <x-forms.input :label="$label . __('URL (internal)')" disabled value="{{ __('Locked (shown once)') }}"
            helper="{{ __('The password variable is locked, so the URL that contains it is not shown.') }}" />
    @else
        <x-forms.input :label="$label . __('URL (internal)')" :helper="$urlHelper" type="password" readonly copyable
            wire:model="dbUrl" canGate="update" :canResource="$database" />
        @if ($dbUrlPublic)
            <x-forms.input :label="$label . __('URL (public)')" :helper="$urlHelper" type="password" readonly copyable
                wire:model="dbUrlPublic" canGate="update" :canResource="$database" />
        @elseif ($showPublicUrlPlaceholder)
            <x-forms.input :label="$label . __('URL (public)')" :helper="$urlHelper" readonly
                value="{{ __('database.keydb_url_generate_hint') }}" canGate="update" :canResource="$database" />
        @endif
    @endif

    @if ($supportsSsl)
        <div class="border-t border-neutral-200 pt-5 dark:border-white/[0.06]">
            <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-black dark:text-fg">{{ __('SSL configuration') }}</h3>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-fg-dim">
                            {{ __('Encryption settings can only be changed while the database is stopped.') }}
                        </p>
                    </div>
                    @if ($enableSsl && $certificateValidUntil)
                        <x-modal-confirmation title="{{ __('database.regenerate_ssl_certs') }}"
                            buttonTitle="{{ __('database.regenerate_ssl_certs') }}" :actions="[
                                __('database.regenerate_ssl_action_1'),
                                __('database.regenerate_ssl_action_2'),
                            ]"
                            submitAction="regenerateSslCertificate" :confirmWithText="false" :confirmWithPassword="false" />
                    @endif
            </div>
            @if ($enableSsl && $certificateValidUntil)
                <div class="mb-4 text-sm text-neutral-600 dark:text-fg-dim">{{ __('database.valid_until') }}
                    @if (now()->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} {{ __('- Expired') }}</span>
                    @elseif(now()->addDays(30)->gt($certificateValidUntil))
                        <span class="text-red-500">{{ $certificateValidUntil->format('d.m.Y H:i:s') }} {{ __('- Expiring soon') }}</span>
                    @else
                        <span>{{ $certificateValidUntil->format('d.m.Y H:i:s') }}</span>
                    @endif
                </div>
            @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <x-forms.listbox canGate="update" :canResource="$database" id="enableSsl" label="{{ __('SSL') }}"
                    onChange="instantSaveSSL"
                    :disabled="! $isExited || ! auth()->user()?->can('update', $database)"
                    :options="[
                        ['value' => true, 'label' => __('scheduled_task.enabled')],
                        ['value' => false, 'label' => __('Disabled')],
                    ]" />
                @if ($sslModeOptions)
                    <x-forms.listbox canGate="update" :canResource="$database" id="sslMode" label="{{ __('SSL mode') }}" :helper="$sslModeHelper"
                        onChange="instantSaveSSL"
                        :disabled="! $enableSsl || ! $isExited || ! auth()->user()?->can('update', $database)"
                        :options="collect($sslModeOptions)->map(fn ($option, $value) => [
                            'value' => $value,
                            'label' => $option['label'],
                        ])->values()->all()" />
                @endif
            </div>
        </div>
    @endif
</div>
