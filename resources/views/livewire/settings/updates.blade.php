<div>
    <x-slot:title>
        {{ __('Update Settings | Coolify') }}
    </x-slot>

    <x-settings.layout>
        <form wire:submit="submit" class="application-settings-form flex min-w-0 flex-col gap-6">
            {{-- Exclude is_auto_update_enabled (instantSave) so the bar does not flash. --}}
            <x-unsaved-bar action="submit" targets="update_check_frequency,auto_update_frequency,update_channel,update_custom_tag" />

            <x-application.settings-section title="{{ __('settings.updates') }}"
                helper="{{ __('Install the latest Coolify version manually when an update is available.') }}">
                <x-slot:actions>
                    <livewire:upgrade :full-button="true" key="settings-upgrade" />
                    <x-forms.button type="button" wire:click="checkManually">
                        <x-reicon name="refresh" class="size-3.5" />
                        {{ __('Check now') }}
                    </x-forms.button>
                </x-slot:actions>
                <div class="grid gap-4 lg:grid-cols-2">
                    {{-- Not live: a live update clears the dirty state, so the unsaved bar never opened for a channel change. --}}
                    <x-forms.listbox id="update_channel" label="{{ __('Channel') }}" :options="[
                        ['value' => 'stable', 'label' => __('Stable'), 'description' => __('Stable releases')],
                        ['value' => 'preview', 'label' => __('Preview'), 'description' => __('Betas and release candidates, then the stable release')],
                        ['value' => 'nightly', 'label' => __('Nightly'), 'description' => __('Early access to the next version, two builds each day. Less tested')],
                        ['value' => 'custom', 'label' => __('Custom image'), 'description' => __('A pinned image, such as the build of a pull request')],
                    ]"
                        helper="{{ __('The versions this instance upgrades to.') }}" />

                    <div x-show="$wire.update_channel === 'custom'" @if ($update_channel !== 'custom') x-cloak @endif>
                        <x-forms.input required id="update_custom_tag" label="{{ __('Image tag') }}" placeholder="{{ __('pr-1234') }}"
                            x-bind:required="$wire.update_channel === 'custom'"
                            helper="{{ __('A tag of ghcr.io/coollabsio/coolify, such as pr-1234 for the build of pull request 1234.') }}" />
                    </div>
                    <div x-show="$wire.update_channel !== 'custom'" @if ($update_channel === 'custom') x-cloak @endif
                        class="hidden lg:block"></div>

                    <div x-show="$wire.update_channel === 'stable'" @if ($update_channel !== 'stable') x-cloak @endif
                        class="lg:col-span-2">
                        <x-callout type="info" title="{{ __('Try the next version early') }}">
                            {{ __('The Nightly channel installs a build of the next version two times each day. Nightly builds get less testing and can break this instance.') }}
                        </x-callout>
                    </div>
                    <div x-show="$wire.update_channel === 'nightly'" @if ($update_channel !== 'nightly') x-cloak @endif
                        class="lg:col-span-2">
                        <x-callout type="danger" title="{{ __('Nightly builds can break this instance') }}">
                            {{ __('Nightly builds are untested builds of the next version. Things can break at any time and breaking changes are possible, including database changes a stable release does not expect. Back up first and use nightly only on an instance you can rebuild. After switching back to stable, the instance keeps getting the nightlies of its version until that version is released as stable.') }}
                        </x-callout>
                    </div>
                    <div x-show="$wire.update_channel === 'custom'" @if ($update_channel !== 'custom') x-cloak @endif
                        class="lg:col-span-2">
                        <x-callout type="danger" title="{{ __('A custom image can break this instance') }}">
                            {{ __('The image installs only when you upgrade to it here, and automatic updates pause until you switch back to a channel. Pull request builds are untested and can include database changes a later release does not expect.') }}
                        </x-callout>
                    </div>

                    <x-forms.input required id="update_check_frequency" label="{{ __('Check frequency') }}"
                        placeholder="0 * * * *"
                        helper="{{ __('A cron expression or preset such as hourly, daily, weekly, monthly, or yearly.') }}" />

                    <x-forms.listbox id="docker_registry_url" label="{{ __('Docker registry') }}" :options="[
                        ['value' => 'docker.io', 'label' => __('Docker Hub')],
                        ['value' => 'ghcr.io', 'label' => __('GitHub Container Registry')],
                    ]"
                        helper="{{ __('Switch registries if the current source is rate limited.') }}" />

                    @if (!is_null(config('constants.coolify.autoupdate', null)))
                        <x-forms.listbox disabled id="is_auto_update_enabled" label="{{ __('Automatic updates') }}"
                            helper="{{ __('Controlled by the AUTOUPDATE environment variable.') }}" :options="[
                                ['value' => true, 'label' => __('scheduled_task.enabled')],
                                ['value' => false, 'label' => __('Disabled')],
                            ]" />
                    @else
                        <x-forms.listbox id="is_auto_update_enabled" label="{{ __('Automatic updates') }}"
                            onChange="instantSave" :options="[
                                ['value' => true, 'label' => __('scheduled_task.enabled')],
                                ['value' => false, 'label' => __('Disabled')],
                            ]" />
                    @endif

                    @if (is_null(config('constants.coolify.autoupdate', null)) && $is_auto_update_enabled)
                        <x-forms.input required id="auto_update_frequency" label="{{ __('Update frequency') }}"
                            placeholder="0 0 * * *"
                            helper="{{ __('Cron expression or preset for installing updates.') }}" />
                    @else
                        <x-forms.input label="{{ __('Update frequency') }}" disabled placeholder="{{ __('Disabled') }}" />
                    @endif
                </div>
            </x-application.settings-section>
        </form>
    </x-settings.layout>
</div>
