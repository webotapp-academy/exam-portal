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
        <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 bg-gradient-to-br from-slate-50 via-indigo-50/40 to-violet-50/60">
            <div class="mb-6 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white text-xl font-extrabold shadow-lg shadow-indigo-500/30">E</span>
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900">Exam<span class="text-indigo-600">Portal</span></span>
                </a>
                <p class="mt-1.5 text-xs font-medium text-slate-500">Online MCQ Assessments &amp; Instant Practice</p>
            </div>

            <div class="w-full sm:max-w-md px-7 py-7 bg-white shadow-xl shadow-slate-200/70 border border-slate-200/80 overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>

            <div class="mt-6 w-full sm:max-w-md rounded-xl bg-white/80 border border-slate-200 px-4 py-3 text-xs text-slate-600">
                <div class="font-semibold text-slate-800 mb-1">Demo Accounts (Password: <code class="px-1.5 py-0.5 rounded bg-slate-100 text-indigo-700 font-mono">password</code>)</div>
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    <span>Admin: <code class="text-slate-800">admin@examportal.test</code></span>
                    <span>Student: <code class="text-slate-800">student@examportal.test</code></span>
                </div>
            </div>
        </div>
    </body>
</html>
