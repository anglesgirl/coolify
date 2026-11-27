@props(['database', 'currentRoute', 'navOnly' => false])

@php
    $databaseRouteParameters = [
        'project_uuid' => $database->environment->project->uuid,
        'environment_uuid' => $database->environment->uuid,
        'database_uuid' => $database->uuid,
    ];

    $configurationItems = collect([
        ['label' => __('menu.general'), 'route' => 'project.database.configuration', 'icon' => 'settings'],
        ['label' => __('menu.environment_variables'), 'route' => 'project.database.environment-variables', 'icon' => 'variables'],
        ['label' => __('menu.persistent_storage'), 'route' => 'project.database.persistent-storage', 'icon' => 'storages'],
        ['label' => __('menu.backups'), 'route' => 'project.database.backup.index', 'icon' => 'database', 'visible' => $database->isBackupSolutionAvailable()],
        ['label' => __('menu.servers'), 'route' => 'project.database.servers', 'icon' => 'servers'],
        ['label' => __('Runtime Logs'), 'route' => 'project.database.logs', 'icon' => 'unordered-list', 'navigate' => false],
        ['label' => __('menu.terminal'), 'route' => 'project.database.command', 'icon' => 'browser-terminal', 'navigate' => false, 'visible' => auth()->user()?->can('canAccessTerminal')],
        ['label' => __('storage.files'), 'route' => 'project.database.files', 'icon' => 'folder', 'visible' => auth()->user()?->can('canAccessTerminal')],
        ['label' => __('webhooks.title'), 'route' => 'project.database.webhooks', 'icon' => 'webhook'],
        ['label' => __('application.healthcheck'), 'route' => 'project.database.healthcheck', 'icon' => 'feedback'],
        ['label' => __('resource_limits.title'), 'route' => 'project.database.resource-limits', 'icon' => 'cpu'],
        ['label' => __('resource_ops.title'), 'route' => 'project.database.resource-operations', 'icon' => 'server-update'],
        ['label' => __('metrics.title'), 'route' => 'project.database.metrics', 'icon' => 'graph'],
        ['label' => __('Security'), 'route' => 'project.database.vulnerabilities', 'icon' => 'shield-alert',
            'security' => \App\Services\ImageScan\SecurityStatus::stored($database)->status->value],
        ['label' => __('tags.title'), 'route' => 'project.database.tags', 'icon' => 'tags'],
        ['label' => __('menu.danger_zone'), 'route' => 'project.database.danger', 'icon' => 'danger'],
    ])->filter(fn (array $item): bool => $item['visible'] ?? true)
        ->map(fn (array $item): array => [
            ...$item,
            'active' => $currentRoute === $item['route']
                || ($item['route'] === 'project.database.backup.index'
                    && str($currentRoute)->startsWith('project.database.backup')),
        ]);

    $menuGroups = [
        'Settings' => ['General', 'Environment Variables', 'Persistent Storage', 'Healthcheck'],
        'Observe & troubleshoot' => ['Runtime Logs', 'Terminal', 'Files', 'Metrics', 'Security'],
        'Deploy' => ['Servers'],
        'Automation' => ['Webhooks', 'Backups'],
        'Operations' => ['Resource Operations', 'Resource Limits', 'Tags'],
        'Danger Zone' => ['Danger Zone'],
    ];

    $groupedItems = collect($menuGroups)
        ->map(fn (array $labels) => collect($labels)
            ->map(fn (string $label) => $configurationItems->firstWhere('label', $label))
            ->filter()
            ->values())
        ->filter(fn ($items) => $items->isNotEmpty());

    $pageSections = array_values(array_filter([
        ['id' => 'database-details-section', 'label' => __('Database details')],
        $database->type() === 'standalone-sqlite' ? null : ['id' => 'access-section', 'label' => __('Access')],
        ['id' => 'container-section', 'label' => __('execute.container_label')],
        $database->type() === 'standalone-postgresql'
            ? ['id' => 'initialization-scripts-section', 'label' => __('database.initialization_scripts')]
            : null,
    ]));
@endphp

@php
    // The main sidebar and the mobile drawer show this menu (see components/navbar.blade.php).
    registerMainNavContext(0, 'Dashboard', 'Back to the main menu', $database->name);
@endphp

{{-- Rendered twice: in the page (CSS hides it; it keeps the command palette items), and teleported into the main sidebar.
     navOnly renders only the sidebar copy, as the parent menu of a deeper page (e.g. a backup). --}}
@foreach ($navOnly ? ['main-nav'] : ['page', 'main-nav'] as $placement)
@if ($placement === 'main-nav')
@teleport('#main-nav-context-0')
<div class="nav-context-menu">
@else
<aside class="application-settings-navigation in-main-nav min-w-0"
    data-settings-search-items="{{ json_encode(settingsSearchItems($groupedItems, $databaseRouteParameters, ['project.database.configuration' => $pageSections])) }}">
@endif
    <nav aria-label="{{ __('Database settings') }}"
        class="grid grid-cols-1 gap-0.5">
        @foreach ($groupedItems as $groupLabel => $groupItems)
            @unless ($loop->first)
                <div class="my-2 border-t border-neutral-200 dark:border-white/[0.06]" aria-hidden="true"></div>
            @endunless
            @foreach ($groupItems as $menuItem)
                <a @class(['menu-item', 'menu-item-active' => $menuItem['active']])
                    @if ($menuItem['navigate'] ?? true) {{ wireNavigate() }} @endif
                    href="{{ route($menuItem['route'], $databaseRouteParameters) }}">
                    <x-reicon :name="$menuItem['icon']" class="menu-item-icon" />
                    <span class="menu-item-label">{{ $menuItem['label'] }}</span>
                    @if ($menuItem['label'] === 'Files')
                        <x-beta-badge class="ml-auto shrink-0" />
                    @endif
                    <x-image-scan.security-dot :status="$menuItem['security'] ?? null" />
                </a>
                @if ($menuItem['active'] && $menuItem['route'] === 'project.database.configuration' && $pageSections !== [])
                    <div class="nav-children flex flex-col gap-0.5 py-1"
                        x-data="{
                            activeSection: '',
                            scrollToSection(id) {
                                this.activeSection = id;
                                window.scrollToSettingsSection?.(id);
                            },
                        }">
                        @foreach ($pageSections as $section)
                            <button type="button" class="menu-subitem"
                                :class="activeSection === '{{ $section['id'] }}' && 'menu-subitem-active'"
                                @click="scrollToSection('{{ $section['id'] }}')">
                                <span class="menu-item-label text-left">{{ $section['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach
    </nav>
@if ($placement === 'main-nav')
</div>
@endteleport
@else
</aside>
@endif
@endforeach
