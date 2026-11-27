<div class="w-full max-h-[70vh] overflow-y-auto pr-1 pt-1">
    <div class="flex flex-col gap-6">
        <div>
            <h3>{{ __('resource') }}</h3>
            <div class="pt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                <x-forms.copy-input :label="__('scheduled_task.name_label')" :text="$resource->name ?? ''" />
                <x-forms.copy-input :label="__('UUID')" :text="$resource->uuid ?? ''" />
            </div>
        </div>

        @if ($environment_uuid)
            <div>
                <h3>{{ __('Environment') }}</h3>
                <div class="pt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <x-forms.copy-input :label="__('scheduled_task.name_label')" :text="$environment_name ?? ''" />
                    <x-forms.copy-input :label="__('UUID')" :text="$environment_uuid" />
                </div>
            </div>
        @endif

        @if ($project_uuid)
            <div>
                <h3>{{ __('Project') }}</h3>
                <div class="pt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <x-forms.copy-input :label="__('scheduled_task.name_label')" :text="$project_name ?? ''" />
                    <x-forms.copy-input :label="__('UUID')" :text="$project_uuid" />
                </div>
            </div>
        @endif

        @if ($server_uuid)
            <div>
                <h3>{{ __('Server') }}</h3>
                <div class="pt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                    <x-forms.copy-input :label="__('scheduled_task.name_label')" :text="$server_name ?? ''" />
                    <x-forms.copy-input :label="__('UUID')" :text="$server_uuid" />
                </div>
            </div>
        @endif

        @if (! empty($stack_applications) || ! empty($stack_databases))
            <div>
                <h3>{{ __('Stack Sub-Resources') }}</h3>
                <div class="pt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                    @foreach ($stack_applications as $item)
                        <x-forms.copy-input :label="__('Application: ') . $item['name']" :text="$item['uuid']" />
                    @endforeach
                    @foreach ($stack_databases as $item)
                        <x-forms.copy-input :label="__('Database: ') . $item['name']" :text="$item['uuid']" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
