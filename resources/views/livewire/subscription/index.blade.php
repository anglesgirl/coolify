<div class="application-settings-form w-full max-w-none">
    <x-slot:title>
        {{ __('subscription.title') }}
    </x-slot>

    <x-dashboard.navbar section="subscription" title="{{ __('navbar.subscription') }}"
        subtitle="{{ __('Choose a plan for Coolify Cloud') }}" />

    @if (auth()->user()->isAdminFromSession())
        @if ($loading)
            <div wire:init="getStripeStatus">
                <x-skeleton.settings-sections :count="2" label="{{ __('Loading your subscription status') }}" />
            </div>
        @else
            @if ($isUnpaid)
                <x-application.settings-section title="{{ __('Payment failed') }}"
                    description="{{ __('Your latest Coolify Cloud payment could not be processed.') }}">
                    <x-callout type="danger" title="{{ __('Subscription payment is past due') }}">
                        {{ __('Update the payment method or settle the outstanding invoice in the billing portal.') }}
                    </x-callout>
                    <div class="mt-4">
                        <x-forms.button wire:click="stripeCustomerPortal" isHighlighted>{{ __('Open billing portal') }}</x-forms.button>
                    </div>
                </x-application.settings-section>
            @else
                @if ($isCancelled || ! data_get(currentTeam(), 'subscription'))
                    <x-callout type="warning" title="{{ __('No active subscription') }}" class="mb-6">
                        {{ __('Choose a plan to continue using Coolify Cloud.') }}
                    </x-callout>
                @endif
                {{-- Stripe is the only cloud provider; always render pricing so the page is never blank. --}}
                <livewire:subscription.pricing-plans />
            @endif
        @endif
    @else
        <x-application.settings-section title="{{ __('navbar.subscription') }}"
            description="{{ __('Only team administrators can manage billing and plan limits.') }}">
            <x-callout type="danger" title="{{ __('danger.insufficient_permissions') }}">
                {{ __('subscription.not_admin') }}
                <span class="underline cursor-pointer dark:text-white" wire:click="help">{{ __('subscription.contact_us') }}</span>.
            </x-callout>
        </x-application.settings-section>
    @endif
</div>
