<div class="mt-8 w-full lg:mt-3" x-data="{ showNormalTextarea: false }">
    <form wire:submit="submit">
        <section class="application-settings-section">
            <div class="application-settings-section-header">
                <div>
                    <h2>{{ __('application.docker_compose') }}</h2>
                    <p>{{ __('Create a multi-container service directly from a Compose file.') }}</p>
                </div>
                <x-forms.button type="submit" wire:target="submit" isHighlighted>{{ __('Create service') }}</x-forms.button>
            </div>
            <div class="application-settings-section-body">
                <div x-cloak x-show="showNormalTextarea">
                    <x-forms.textarea label="{{ __('Docker Compose file') }}" rows="20" id="dockerComposeRaw" placeholder='services:
  app:
    image: nginx:alpine
    ports:
      - "80"
'></x-forms.textarea>
                </div>
                <div x-cloak x-show="!showNormalTextarea">
                    <x-forms.textarea allowTab useMonacoEditor monacoEditorLanguage="yaml"
                        label="{{ __('Docker Compose file') }}" rows="20" id="dockerComposeRaw" autofocus placeholder='services:
  app:
    image: nginx:alpine
    ports:
      - "80"
'></x-forms.textarea>
                </div>
                <div
                    class="mt-2 flex flex-col items-stretch gap-2 rounded-lg border border-neutral-200 bg-neutral-50 p-1 sm:flex-row sm:flex-wrap sm:items-center dark:border-white/[0.08] dark:bg-white/[0.05]">
                    <x-forms.checkbox label="{{ __('Use plain-text editor') }}" id="showNormalTextarea"
                        x-model="showNormalTextarea" />
                </div>
            </div>
        </section>
    </form>
</div>
