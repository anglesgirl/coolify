@php
    $settingsMenuSections = [
        __('menu.configuration') => [
            ['label' => __('menu.general'), 'route' => 'settings.index', 'icon' => 'settings'],
            ['label' => __('menu.advanced'), 'route' => 'settings.advanced', 'icon' => 'grid'],
            ['label' => __('settings.updates'), 'route' => 'settings.updates', 'icon' => 'refresh3'],
            ['label' => __('Image Scanning'), 'route' => 'settings.image-scanning', 'icon' => 'shield-alert'],
        ],
        __('Instance') => [
            ['label' => __('Authentication'), 'route' => 'settings.oauth', 'icon' => 'keys'],
            ['label' => __('input.email'), 'route' => 'settings.email', 'icon' => 'mail'],
            ['label' => __('backup.backup_suffix'), 'route' => 'settings.backup', 'icon' => 'database'],
        ],
    ];
@endphp

{{-- The pages are in the Settings submenu of the main sidebar (components/navbar.blade.php). --}}
<section class="application-settings-workspace w-full max-w-none">
    <header class="settings-mobile-header xl:hidden">
        <h1 class="settings-mobile-title">{{ __('Instance Settings') }}</h1>
        <p class="settings-mobile-description">{{ __('Configure global settings for this Coolify instance.') }}</p>
    </header>
    {{-- Feeds the page search with the settings pages. --}}
    <div hidden data-settings-search-items="{{ json_encode(settingsSearchItems($settingsMenuSections, [])) }}"></div>
    @isset($submenu)
        {{-- Sub-items of the current page, under its entry in the main sidebar. --}}
        @teleport('#main-nav-active-children')
            <div>{{ $submenu }}</div>
        @endteleport
    @endisset
    <div class="min-w-0">
        {{ $slot }}
    </div>
</section>
