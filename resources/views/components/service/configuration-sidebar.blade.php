@props(['service', 'currentRoute', 'navOnly' => false, 'storageSections' => collect()])

@php
    $serviceRouteParameters = [
        'project_uuid' => $service->environment->project->uuid,
        'environment_uuid' => $service->environment->uuid,
        'service_uuid' => $service->uuid,
    ];

    $configurationItems = collect([
        ['label' => __('menu.general'), 'route' => 'project.service.configuration', 'icon' => 'settings'],
        ['label' => __('application.domains'), 'route' => 'project.service.domains', 'icon' => 'globe'],
        ['label' => __('menu.environment_variables'), 'route' => 'project.service.environment-variables', 'icon' => 'variables', 'hasWarning' => ! $service->isDeployable],
        ['label' => __('menu.persistent_storage'), 'route' => 'project.service.storages', 'icon' => 'storages'],
        ['label' => __('menu.backups'), 'route' => 'project.service.volume-backups.index', 'icon' => 'database'],
        ['label' => __('Runtime Logs'), 'route' => 'project.service.logs', 'icon' => 'unordered-list', 'navigate' => false],
        ['label' => __('menu.terminal'), 'route' => 'project.service.command', 'icon' => 'browser-terminal', 'navigate' => false, 'visible' => auth()->user()?->can('canAccessTerminal')],
        ['label' => __('storage.files'), 'route' => 'project.service.files', 'icon' => 'folder', 'visible' => auth()->user()?->can('canAccessTerminal')],
        ['label' => __('Analytics'), 'route' => 'project.service.analytics', 'icon' => 'analytics'],
        ['label' => __('menu.scheduled_tasks'), 'route' => 'project.service.scheduled-tasks.show', 'icon' => 'calendar'],
        ['label' => __('webhooks.title'), 'route' => 'project.service.webhooks', 'icon' => 'webhook'],
        ['label' => __('resource_ops.title'), 'route' => 'project.service.resource-operations', 'icon' => 'server-update'],
        ['label' => __('Maintenance Mode'), 'route' => 'project.service.maintenance', 'icon' => 'bandage'],
        ['label' => __('Security'), 'route' => 'project.service.vulnerabilities', 'icon' => 'shield-alert',
            'security' => \App\Services\ImageScan\SecurityStatus::stored($service)->status->value],
        ['label' => __('tags.title'), 'route' => 'project.service.tags', 'icon' => 'tags'],
        ['label' => __('menu.danger_zone'), 'route' => 'project.service.danger', 'icon' => 'danger'],
    ])->filter(fn (array $item): bool => $item['visible'] ?? true)
        ->map(fn (array $item): array => [
            ...$item,
            'active' => $currentRoute === $item['route']
                || ($item['route'] === 'project.service.scheduled-tasks.show'
                    && str($currentRoute)->startsWith('project.service.scheduled-tasks'))
                || ($item['route'] === 'project.service.volume-backups.index'
                    && str($currentRoute)->startsWith('project.service.volume-backups')),
        ]);

    $menuGroups = [
        'Settings' => ['General', 'Domains', 'Environment Variables', 'Persistent Storage'],
        'Observe & troubleshoot' => ['Runtime Logs', 'Terminal', 'Files', 'Analytics', 'Security'],
        'Automation' => ['Scheduled Tasks', 'Webhooks', 'Backups'],
        'Operations' => ['Resource Operations', 'Maintenance Mode', 'Tags'],
        'Danger Zone' => ['Danger Zone'],
    ];

    $groupedItems = collect($menuGroups)
        ->map(fn (array $labels) => collect($labels)
            ->map(fn (string $label) => $configurationItems->firstWhere('label', $label))
            ->filter()
            ->values())
        ->filter(fn ($items) => $items->isNotEmpty());
@endphp

<x-main-nav-menu back-label="Dashboard" :name="$service->name" :nav-only="$navOnly"
    data-settings-search-items="{{ json_encode(settingsSearchItems($groupedItems, $serviceRouteParameters)) }}">
    <nav aria-label="{{ __('Service settings') }}"
        class="grid grid-cols-1 gap-0.5">
        @foreach ($groupedItems as $groupLabel => $groupItems)
            @unless ($loop->first)
                <div class="my-2 border-t border-neutral-200 dark:border-white/[0.06]" aria-hidden="true"></div>
            @endunless
            @foreach ($groupItems as $menuItem)
                <a @class(['menu-item', 'menu-item-active' => $menuItem['active']])
                    @if ($menuItem['navigate'] ?? true) {{ wireNavigate() }} @endif
                    href="{{ route($menuItem['route'], $serviceRouteParameters) }}">
                    <x-reicon :name="$menuItem['icon']" class="menu-item-icon" />
                    <span class="menu-item-label">{{ $menuItem['label'] }}</span>
                    @if ($menuItem['label'] === 'Files')
                        <x-beta-badge class="ml-auto shrink-0" />
                    @endif
                    <x-image-scan.security-dot :status="$menuItem['security'] ?? null" />
                    @if ($menuItem['hasWarning'] ?? false)
                        <span class="ml-auto size-2 shrink-0 rounded-full bg-error" title="{{ __('Required environment variables missing') }}"></span>
                    @endif
                </a>
                @if ($menuItem['active'] && $menuItem['route'] === 'project.service.storages' && $storageSections->isNotEmpty())
                    <div class="nav-children flex flex-col gap-0.5 py-1"
                        x-data="{ activeSection: '' }">
                        @foreach ($storageSections as $section)
                            <button type="button" class="menu-subitem"
                                :class="activeSection === '{{ $section['id'] }}' && 'menu-subitem-active'"
                                @click="activeSection = '{{ $section['id'] }}'; window.scrollToSettingsSection?.('{{ $section['id'] }}')">
                                <span class="menu-item-label truncate text-left"
                                    title="{{ $section['label'] }}">{{ $section['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach
    </nav>
</x-main-nav-menu>
