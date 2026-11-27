@extends('proxy.layout')

@section('title', 'Site not available')

@section('content')
    <p class="status" aria-label="{{ __('503 Service Unavailable') }}">503</p>
    <h1>{{ __('This site is not available at the moment') }}</h1>
    <p><span id="host">{{ __('This site') }}</span> {{ __('cannot answer your request right now. Please try again later.') }}</p>
@endsection
