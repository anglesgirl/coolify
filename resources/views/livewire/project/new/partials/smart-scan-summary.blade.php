{{--
    Smart Scan result inside the build configuration, as a compact divided list: the build pack,
    the Dockerfile port, and the env file import. The scan button sends the current base directory,
    so the user can scan a subdirectory after changing it.
--}}
@php
    $buildPackLabels = ['dockercompose' => __('application.docker_compose'), 'dockerfile' => __('application.dockerfile')];
    $suggestedFile = match ($suggestedBuildPack) {
        'dockercompose' => $detectedDockerComposeFiles[0] ?? null,
        'dockerfile' => $detectedDockerfiles[0] ?? null,
        default => null,
    };
    $nestedFiles = collect([
        count($detectedDockerfiles) ? count($detectedDockerfiles).' '.Str::plural('Dockerfile', count($detectedDockerfiles)) : null,
        count($detectedDockerComposeFiles) ? count($detectedDockerComposeFiles).' '.Str::plural('Compose file', count($detectedDockerComposeFiles)) : null,
    ])->filter()->join(' and ');
    $showPort = $detectionRan && $build_pack === 'dockerfile' && in_array($selectedDockerfile, $detectedDockerfiles, true);
@endphp

<div class="overflow-hidden rounded-[10px] border border-neutral-200 dark:border-white/[0.08]">
    <div class="flex min-h-10 items-center justify-between gap-3 border-b border-neutral-200 py-1.5 pr-1.5 pl-4 dark:border-white/[0.08]">
        <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-neutral-400 dark:text-fg-faint">
            {{ __('Smart Scan') }}
        </p>
        <x-forms.button type="button" wire:click="detectRepository">
            <x-reicon name="search" class="size-3.5" />
            {{ $detectionRan ? __('Scan again') : __('Scan repository') }}
        </x-forms.button>
    </div>

    <div wire:loading.block wire:target="detectRepository">
        <x-checkpoint-item status="running" title="{{ __('Scanning the repository') }}"
            description="{{ __('Looking for Dockerfiles, Compose files, and env files.') }}" />
    </div>

    <div wire:loading.remove wire:target="detectRepository" class="divide-y divide-neutral-200 dark:divide-white/[0.07]">
        @if (! $detectionRan)
            <x-checkpoint-item icon="search" title="{{ __('Not scanned yet') }}"
                description="{{ __('Scan the repository to find Dockerfiles, Compose files, and env files.') }}" />
        @elseif ($detectionFailed)
            <x-checkpoint-item status="error" title="{{ __('Could not read the repository') }}"
                description="{{ __('Check the repository access and the branch, or select the build pack manually.') }}" />
        @elseif ($suggestedBuildPack)
            <x-checkpoint-item status="success"
                title="{{ $buildPackLabels[$suggestedBuildPack] }} {{ $build_pack === $suggestedBuildPack ? __('selected') : __('suggested') }}"
                :description="__('Found :file in the base directory.', ['file' => $suggestedFile])" />
        @elseif ($nestedFiles !== '')
            <x-checkpoint-item icon="brain" :title="__('Found :files in subdirectories', ['files' => $nestedFiles])" />
        @else
            <x-checkpoint-item icon="brain" title="{{ __('No Dockerfile or Compose file found') }}"
                description="{{ __('Select the build pack that fits the repository.') }}" />
        @endif

        @if ($showPort)
            @if ($detectedPort)
                <x-checkpoint-item status="success" :title="__('Port :port', ['port' => $detectedPort])"
                    :description="__('From EXPOSE in :file.', ['file' => $selectedDockerfile])" />
            @else
                <x-checkpoint-item icon="globe" :title="__('Port :port', ['port' => $port])"
                    :description="__(':file has no EXPOSE instruction, so the default port is used.', ['file' => $selectedDockerfile])" />
            @endif
        @endif

        @if ($detectionRan && count($envExampleVars) > 0)
            <div class="flex items-center justify-between gap-3 pr-4">
                <x-checkpoint-item :status="$envImported ? 'success' : 'idle'" icon="variables"
                    title="{{ $selectedEnvFile }}"
                    :description="__(':count :vars:imported.', ['count' => count($envExampleVars), 'vars' => count($envExampleVars) === 1 ? __('variable') : __('variables'), 'imported' => $envImported ? __(', imported when you continue') : ''])" />
                @include('livewire.project.new.partials.env-import-modal')
            </div>
        @endif
    </div>
</div>
