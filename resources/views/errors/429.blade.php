@extends('layouts.base')

@section('body')
    <body class="error-page-body text-black dark:text-inherit">
        <x-toast />
        <x-error-page
            code="429"
            title="{{ __('error.429.title') }}"
            description="{{ __('error.429.body') }}" />
    </body>
@endsection
