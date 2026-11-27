@extends('proxy.layout')

@section('title', 'Under maintenance')

@section('content')
    <p class="status" aria-label="{{ __('503 Service Unavailable') }}">503</p>
    <h1>{{ __('We are doing some maintenance') }}</h1>
    <p><span id="host">{{ __('This site') }}</span> {{ __('is temporarily unavailable for maintenance. Please check back soon.') }}</p>
@endsection
