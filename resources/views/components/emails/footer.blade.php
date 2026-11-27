{{ Illuminate\Mail\Markdown::parse('---') }}

{{ __('Thank you,') }}<br>
{{ config('app.name') ?? __('Coolify') }}

{{ Illuminate\Mail\Markdown::parse('[Contact Support](https://coolify.io/docs/contact)') }}
