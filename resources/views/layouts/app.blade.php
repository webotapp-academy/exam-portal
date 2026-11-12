<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ExamPortal') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white/90 backdrop-blur border-b border-slate-200/80">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
                    @if (session('success'))
                        <div class="flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="flex items-center gap-2 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                            <span class="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="flex items-center gap-2 rounded-xl bg-indigo-50 border border-indigo-200 px-4 py-3 text-sm font-medium text-indigo-800 shadow-sm">
                            <span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span>
                            {{ session('info') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 shadow-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{ $slot }}
            </main>

            <footer class="bg-white border-t border-slate-200/80 py-4 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} ExamPortal &bull; Laravel Online MCQ Assessment Platform
            </footer>
        </div>
    </body>
</html>
