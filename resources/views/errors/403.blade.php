@extends('layouts.base')

@section('body')
    <body class="error-page-body text-black dark:text-inherit">
        <x-toast />
        <x-error-page
            code="403"
            title="{{ __('error.401.title') }}"
            description="{{ __('error.401.body') }}" />
    </body>
@endsection
