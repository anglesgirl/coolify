<div>
    @php
        $deletionBlockers = currentTeam()->deletionBlockers();
        $blockerDetails = [
            'projects' => ['label' => __('project'), 'route' => 'project.index'],
            'servers' => ['label' => __('common.load_from_server'), 'route' => 'server.index'],
            'sources' => ['label' => __('Git source'), 'route' => 'source.all'],
        ];
    @endphp
    <x-slot:title>
        {{ __('Team Danger Zone | Coolify') }}
    </x-slot>

    <x-team.settings-layout>
        <div class="application-settings-form">
            <x-application.settings-section id="team-danger-zone" title="{{ __('Danger zone') }}"
                helper="{{ __('Destructive actions for this team cannot be undone.') }}">
                <x-danger-zone title="{{ __('Delete team') }}">
                            @if (auth()->user()->roleInTeam(currentTeam()->id) !== 'owner')
                                <p>
                                    {{ __('Only team owners can delete this team.') }}
                                </p>
                            @elseif (session('currentTeam.id') === 0)
                                <p>
                                    {{ __('The default team cannot be deleted.') }}
                                </p>
                            @elseif(auth()->user()->teams()->count() === 1 || auth()->user()->currentTeam()->personal_team)
                                <p>
                                    {{ __('Your last or personal team cannot be deleted.') }}
                                </p>
                            @elseif(currentTeam()->subscription)
                                <p>
                                    {{ __('Cancel your') }} <a class="font-medium text-coollabs hover:underline dark:text-brand-light"
                                        {{ wireNavigate() }} href="{{ route('subscription.show') }}">{{ __('subscription') }}</a>
                                    {{ __('teams.before_deleting') }}
                                </p>
                            @elseif($deletionBlockers === [])
                                <p>
                                    {{ __('Permanently delete') }} <strong class="font-semibold text-black dark:text-fg">{{ currentTeam()->name }}</strong>
                                    {{ __('from Coolify. This action cannot be undone.') }}
                                </p>
                                <ul class="space-y-1 text-xs">
                                    <li>{{ __('• All members will lose access to this team.') }}</li>
                                    <li>{{ __('• This team cannot be restored from Coolify after deletion.') }}</li>
                                </ul>
                            @else
                                <p>
                                    {{ __('This team still owns:') }}
                                </p>
                                <ul class="space-y-1">
                                    @foreach ($deletionBlockers as $type => $count)
                                        <li>
                                            <a class="font-medium text-coollabs hover:underline dark:text-brand-light"
                                                {{ wireNavigate() }} href="{{ route($blockerDetails[$type]['route']) }}">
                                                {{ $count }} {{ str($blockerDetails[$type]['label'])->plural($count) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <p>
                                    {{ __('Remove or move these resources before deleting the team.') }}
                                </p>
                            @endif
                        <x-slot:action>
                            @if (
                                session('currentTeam.id') !== 0 &&
                                    auth()->user()->roleInTeam(currentTeam()->id) === 'owner' &&
                                    auth()->user()->teams()->count() > 1 &&
                                    !auth()->user()->currentTeam()->personal_team &&
                                    !currentTeam()->subscription &&
                                    $deletionBlockers === [])
                                <x-modal-confirmation title="{{ __('teams.confirm_delete') }}" buttonTitle="{{ __('Delete team') }}"
                                    isErrorButton submitAction="delete"
                                    :actions="[
                                        __('teams.delete_action_warning'),
                                    ]"
                                    confirmationText="{{ currentTeam()->name }}"
                                    confirmationLabel="{{ __('Enter the team name to confirm permanent deletion') }}"
                                    shortConfirmationLabel="{{ __('Team name') }}" :confirmWithPassword="false"
                                    step2ButtonText="{{ __('button.permanently_delete') }}" canGate="delete"
                                    :canResource="$team" />
                            @else
                                <x-forms.button isError disabled tooltip="{{ __('Resolve the requirements shown before deleting this team.') }}">
                                    {{ __('Delete team') }}
                                </x-forms.button>
                            @endif
                        </x-slot:action>
                </x-danger-zone>

                @if (session('currentTeam.id') !== 0 && !currentTeam()->subscription && (currentTeam()->projects->isNotEmpty() || currentTeam()->servers->isNotEmpty()))
                    <div class="mt-4 overflow-hidden rounded-lg border border-neutral-200 dark:border-white/[0.08]">
                        <div class="flex items-center justify-between gap-3 border-b border-neutral-200 px-3 py-2 dark:border-white/[0.08]">
                            <h5 class="text-sm font-medium text-black dark:text-fg">{{ __('Resources') }}</h5>
                            <x-forms.button type="button" wire:click="refreshResources">
                                <x-reicon name="refresh" class="size-3.5" />
                                {{ __('Refresh') }}
                            </x-forms.button>
                        </div>
                        <table class="w-full text-left text-sm">
                            <thead class="bg-neutral-50 text-[11px] uppercase tracking-wide text-neutral-500 dark:bg-coolgray-100 dark:text-fg-dim">
                                <tr>
                                    <th class="px-3 py-2 font-medium">{{ __('Resource') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('scheduled_task.name_label') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-white/[0.08]">
                                @foreach (currentTeam()->projects as $project)
                                    <tr class="text-[13px] text-neutral-600 hover:bg-neutral-50 dark:text-fg-dim dark:hover:bg-white/[0.03]">
                                        <td>
                                            <a class="block px-3 py-2.5" href="{{ route('project.show', ['project_uuid' => $project->uuid]) }}"
                                                target="_blank" rel="noopener noreferrer">{{ __('Project') }}</a>
                                        </td>
                                        <td>
                                            <a class="block px-3 py-2.5 font-medium text-black dark:text-fg"
                                                href="{{ route('project.show', ['project_uuid' => $project->uuid]) }}"
                                                target="_blank" rel="noopener noreferrer">{{ $project->name }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach (currentTeam()->servers as $server)
                                    <tr class="text-[13px] text-neutral-600 hover:bg-neutral-50 dark:text-fg-dim dark:hover:bg-white/[0.03]">
                                        <td>
                                            <a class="block px-3 py-2.5" href="{{ route('server.show', ['server_uuid' => $server->uuid]) }}"
                                                target="_blank" rel="noopener noreferrer">{{ __('Server') }}</a>
                                        </td>
                                        <td>
                                            <a class="block px-3 py-2.5 font-medium text-black dark:text-fg"
                                                href="{{ route('server.show', ['server_uuid' => $server->uuid]) }}"
                                                target="_blank" rel="noopener noreferrer">{{ $server->name }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-application.settings-section>
        </div>
    </x-team.settings-layout>
</div>
