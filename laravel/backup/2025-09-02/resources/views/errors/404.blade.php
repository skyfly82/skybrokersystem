@extends('layouts.app')

@section('title', 'Strona nie została znaleziona')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full">
        <div class="text-center">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="mt-6 text-6xl font-bold text-gray-900">404</h1>
            <p class="mt-4 text-xl text-gray-600">Strona nie została znaleziona</p>
            <p class="mt-2 text-base text-gray-500">
                Przepraszamy, ale strona której szukasz nie istnieje.
            </p>
            <div class="mt-8">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Wróć do strony głównej
                </a>
            </div>
        </div>
    </div>
</div>
@endsection