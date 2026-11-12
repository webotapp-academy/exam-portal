<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ExamPortal') }} — Online MCQ Exam & Instant Practice Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    {{-- Top Navbar --}}
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-slate-900 text-xl tracking-tight">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white text-base shadow-md shadow-indigo-500/25">E</span>
                <span>Exam<span class="text-indigo-600">Portal</span></span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="#features" class="hover:text-indigo-600 transition">Features</a>
                <a href="#exams" class="hover:text-indigo-600 transition">Exams</a>
                <a href="#how-it-works" class="hover:text-indigo-600 transition">How It Works</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold shadow-sm hover:bg-indigo-700 transition">Go to Dashboard &rarr;</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold shadow-sm shadow-indigo-500/25 hover:bg-indigo-700 transition">Start Free</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Split Hero Section with Live Instant-Answer Preview --}}
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-indigo-900/60 via-slate-950 to-slate-950"></div>
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-violet-600/20 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-200 text-xs font-semibold">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Instant Answer Verification &bull; Timed Assessments
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1]">
                    Smarter MCQ Exams. <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-violet-300 to-emerald-300">Instant Clarity.</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-xl">
                    Practice with immediate answer reveals and step-by-step explanations, or take proctored timed exams with live question palettes, leaderboards, and verified PDF scorecards.
                </p>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-7 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg shadow-indigo-600/30 transition">
                            Open Exam Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg shadow-indigo-600/30 transition">
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-white font-semibold backdrop-blur transition">
                            Sign In
                        </a>
                    @endauth
                </div>

                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/10 max-w-md">
                    <div>
                        <div class="text-2xl font-extrabold text-white">2 Modes</div>
                        <div class="text-xs text-slate-400">Instant &amp; Timed</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-emerald-400">Real-Time</div>
                        <div class="text-xs text-slate-400">Auto-Grading</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-indigo-300">PDF</div>
                        <div class="text-xs text-slate-400">Verified Scorecards</div>
                    </div>
                </div>
            </div>

            {{-- Interactive UI Preview Card --}}
            <div class="lg:col-span-6">
                <div class="rounded-2xl bg-white text-slate-900 p-6 shadow-2xl shadow-black/40 border border-slate-200/20">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">Instant Answer Mode</span>
                            <span class="text-xs font-semibold text-slate-400">Question 1 of 5</span>
                        </div>
                        <span class="px-3 py-1 rounded-lg bg-slate-900 text-white font-mono text-xs font-bold">18:42</span>
                    </div>

                    <p class="mt-4 font-bold text-slate-900 text-base">
                        Which method prevents the N+1 query problem when loading related models in Laravel Eloquent?
                    </p>

                    <div class="mt-4 space-y-2.5 text-sm">
                        <div class="flex items-center justify-between p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50/70 font-semibold text-emerald-950">
                            <div class="flex items-center gap-3">
                                <span class="h-6 w-6 rounded-md bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">A</span>
                                <span>Model::with('relation')->get()</span>
                            </div>
                            <span class="text-xs font-bold text-emerald-700">✓ Correct</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 text-slate-500">
                            <span class="h-6 w-6 rounded-md bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center">B</span>
                            <span>Model::lazy('relation')->get()</span>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs text-slate-700 leading-relaxed">
                        <strong class="text-emerald-800 uppercase tracking-wider block mb-1">Instant Explanation (+1.00 Mark)</strong>
                        Eager loading via <code class="px-1 py-0.5 bg-white rounded border border-emerald-200">with()</code> fetches related records in a minimal number of queries instead of running one query per parent model.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Everything You Need in an Exam Portal</h2>
            <p class="mt-2 text-slate-600 text-sm sm:text-base">Built for students preparing for certifications and instructors running structured assessments.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition">
                <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Instant Answer &amp; Explanations</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">Verify answers question-by-question in practice mode with instant color-coded feedback and detailed explanations.</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition">
                <div class="h-11 w-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Timed Exams &amp; Anti-Cheat</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">Server-enforced countdown timers, auto-submission on expiry, randomized questions/options, and tab-switch tracking.</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition">
                <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Leaderboards &amp; PDF Scorecards</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">Compete on exam leaderboards, bookmark tricky MCQs for revision, and download official PDF certificates.</p>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section id="how-it-works" class="bg-white border-y border-slate-200/80 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-10">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="mx-auto h-10 w-10 rounded-full bg-indigo-600 text-white font-extrabold flex items-center justify-center mb-3">1</div>
                    <h4 class="font-bold text-slate-900">Pick an Exam or Practice Set</h4>
                    <p class="mt-1 text-sm text-slate-500">Filter by category or choose Instant Answer vs. Timed Assessment mode.</p>
                </div>
                <div>
                    <div class="mx-auto h-10 w-10 rounded-full bg-indigo-600 text-white font-extrabold flex items-center justify-center mb-3">2</div>
                    <h4 class="font-bold text-slate-900">Answer &amp; Check Live</h4>
                    <p class="mt-1 text-sm text-slate-500">Use the Question Palette, flag questions for review, and check answers instantly.</p>
                </div>
                <div>
                    <div class="mx-auto h-10 w-10 rounded-full bg-indigo-600 text-white font-extrabold flex items-center justify-center mb-3">3</div>
                    <h4 class="font-bold text-slate-900">Review &amp; Download Scorecard</h4>
                    <p class="mt-1 text-sm text-slate-500">Get instant grading, full answer-key explanations, and a verified PDF scorecard.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Exams --}}
    <section id="exams" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Featured Exams</h2>
                <p class="text-sm text-slate-500 mt-0.5">Ready to attempt right now</p>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-indigo-600 hover:underline">View All Exams &rarr;</a>
            @endauth
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredExams as $exam)
                <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl hover:border-indigo-200 transition flex flex-col justify-between">
                    <div class="h-1.5 w-full {{ $exam->isInstantAnswerMode() ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-indigo-500 to-amber-500' }}"></div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3.5">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $exam->category->name ?? 'General' }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $exam->isInstantAnswerMode() ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                    {{ $exam->isInstantAnswerMode() ? 'Instant Answer' : 'Timed Exam' }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $exam->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $exam->description }}</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                            <span>{{ $exam->questions_count }} Questions &bull; {{ $exam->duration_minutes > 0 ? $exam->duration_minutes . ' mins' : 'Untimed' }}</span>
                            <a href="{{ route('exams.show', $exam) }}" class="text-indigo-600 font-bold hover:underline">Start Exam &rarr;</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 col-span-3">No exams published yet.</p>
            @endforelse
        </div>
    </section>

    <footer class="bg-slate-950 text-slate-400 py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <div class="font-bold text-white text-sm">Exam<span class="text-indigo-400">Portal</span></div>
            <div>&copy; {{ date('Y') }} ExamPortal. Built with Laravel &amp; MySQL.</div>
        </div>
    </footer>
</body>
</html>
