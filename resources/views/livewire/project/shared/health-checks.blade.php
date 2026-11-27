@php
    $canUpdate = auth()->user()->can('update', $resource);
@endphp

<form wire:submit="submit" class="application-settings-form flex flex-col gap-6">
    <x-unsaved-bar action="submit" />

    <x-application.settings-section id="healthcheck-configuration-section" :title="__('application.healthcheck')"
        :helper="__('Define how Coolify determines whether this application is ready to receive traffic.')">
        <x-slot:actions>
            @if (!$healthCheckEnabled)
                <x-modal-confirmation :disabled="!auth()->user()->can('update', $resource)"
                    :authDisabled="!auth()->user()->can('update', $resource)"
                    :title="__('Enable healthcheck?')" :buttonTitle="__('application.enable')"
                    submitAction="toggleHealthcheck" :actions="[__('health_check.enable_action')]"
                    :warningMessage="__('If the healthcheck fails, your application will become inaccessible. Review the ') . '<a href=\'https://coolify.io/docs/knowledge-base/health-checks\' target=\'_blank\' class=\'underline text-white\'>' . __('Healthchecks guide') . '</a>' . __(' before continuing.')"
                    :step2ButtonText="__('health_check.enable_button')" :confirmWithText="false"
                    :confirmWithPassword="false" isHighlightedButton />
            @else
                <x-forms.button canGate="update" :canResource="$resource" type="button"
                    wire:click="toggleHealthcheck"> {{ __('button.disable') }} </x-forms.button>
            @endif
        </x-slot:actions>

        @if ($customHealthcheckFound)
            <div class="mb-4">
                @if ($healthCheckEnabled)
                    <x-callout type="warning" :title="__('Dockerfile HEALTHCHECK replaced')"> {{ __('This healthcheck replaces the HEALTHCHECK from the Dockerfile. Disable it to use the Dockerfile HEALTHCHECK again.') }} </x-callout>
                @else
                    <x-callout type="info" :title="__('Dockerfile HEALTHCHECK in use')"> {{ __('The application uses the HEALTHCHECK from its Dockerfile. Enabling this healthcheck replaces it.') }} </x-callout>
                @endif
            </div>
        @endif

        <div class="max-w-xs">
            <x-forms.listbox id="healthCheckType" :label="__('Check type')" required :options="[
                ['value' => 'http', 'label' => __('HTTP request')],
                ['value' => 'cmd', 'label' => __('Container command')],
            ]" :disabled="! $canUpdate" />
        </div>
        <div x-show="$wire.healthCheckType === 'http'" @if ($healthCheckType !== 'http') x-cloak @endif>
            <x-application.settings-subsection id="healthcheck-request-section" :title="__('HTTP request')"
                :helper="__('Coolify sends this request from inside the container and evaluates the response.')">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <x-forms.listbox id="healthCheckMethod" :label="__('health_check.method')" required :options="[
                        ['value' => 'GET', 'label' => 'GET'],
                        ['value' => 'HEAD', 'label' => 'HEAD'],
                        ['value' => 'POST', 'label' => 'POST'],
                        ['value' => 'OPTIONS', 'label' => 'OPTIONS'],
                    ]" :disabled="! $canUpdate" />
                    <x-forms.listbox id="healthCheckScheme" :label="__('health_check.scheme')" required :options="[
                        ['value' => 'http', 'label' => 'HTTP'],
                        ['value' => 'https', 'label' => 'HTTPS'],
                    ]" :disabled="! $canUpdate" />
                    <x-forms.input canGate="update" :canResource="$resource" id="healthCheckHost"
                        placeholder="localhost" :label="__('health_check.host')" required />
                    <x-forms.input canGate="update" :canResource="$resource" type="number"
                        id="healthCheckPort" :helper="__('Uses the first exposed port when empty.')" placeholder="80"
                        :label="__('health_check.port')" />
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_10rem]">
                    <x-forms.input canGate="update" :canResource="$resource" id="healthCheckPath"
                        placeholder="/health" :label="__('health_check.path')" required />
                    <x-forms.input canGate="update" :canResource="$resource" type="number"
                        id="healthCheckReturnCode" placeholder="200"
                        :helper="__('Any 2xx or 3xx response always passes. Set a 4xx or 5xx code (for example 401) to also pass that response.')" :label="__('Expected code')" required />
                </div>
                <div class="mt-4">
                    <x-forms.input canGate="update" :canResource="$resource" id="healthCheckResponseText"
                        :placeholder="__('OK')" :label="__('Expected response text')"
                        :helper="__('The response body must contain this text. Leave empty to check only the response code. A HEAD response has no body, so HEAD does not check the text.')" />
                </div>
            </x-application.settings-subsection>
        </div>
        <div x-show="$wire.healthCheckType !== 'http'" @if ($healthCheckType === 'http') x-cloak @endif>
            <x-application.settings-subsection id="healthcheck-command-section" :title="__('Container command')"
                :helper="__('The command must exit with code 0 for the container to be considered healthy.')">
                <x-callout type="warning" :title="__('Shell operators are not supported')"> {!! __('The command runs directly inside the container. Operators such as ;, |, &amp;, $, &gt;, and &lt; are blocked.') !!} </x-callout>
                <div class="mt-4">
                    <x-forms.input canGate="update" :canResource="$resource" id="healthCheckCommand"
                        :label="__('scheduled_task.command_label')" :placeholder="__('pg_isready -U postgres')"
                        :helper="__('Use a single executable command without shell expansion.')"
                        required x-bind:required="$wire.healthCheckType === 'cmd'" />
                </div>
            </x-application.settings-subsection>
        </div>

        <x-application.settings-subsection id="healthcheck-timing-section" :title="__('Timing and retries')"
            :helper="__('Control how quickly healthchecks start, repeat, and fail.')">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-forms.input canGate="update" :canResource="$resource" min="1" type="number"
                    id="healthCheckInterval" placeholder="30" :label="__('Interval (seconds)')" required />
                <x-forms.input canGate="update" :canResource="$resource" min="1" type="number"
                    id="healthCheckTimeout" placeholder="30" :label="__('scheduled_task.timeout_label')" required />
                <x-forms.input canGate="update" :canResource="$resource" min="1" type="number"
                    id="healthCheckRetries" placeholder="3" :label="__('health_check.retries')" required />
                <x-forms.input canGate="update" :canResource="$resource" min="1" type="number"
                    id="healthCheckStartPeriod" placeholder="30" :label="__('Start period (seconds)')" required />
            </div>
        </x-application.settings-subsection>
    </x-application.settings-section>
</form>
