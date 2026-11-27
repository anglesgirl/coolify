@extends('layouts.base')

@section('body')
    <body class="error-page-body text-black dark:text-inherit">
        <x-toast />
        <x-error-page
            code="503"
            title="{{ __('error.503.title') }}"
            description="{{ __('Service unavailable. Be right back. Thanks for your patience.') }}" />
    </body>
@endsection
