<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Hero Welcome Banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-violet-950 text-white p-6 sm:p-8 shadow-lg">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold border border-white/10">
                        Student Portal &bull; {{ ucfirst(Auth::user()->role) }}
                    </span>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Welcome back, {{ Auth::user()->name }}
                    </h1>
                    <p class="mt-1.5 text-sm text-slate-300 max-w-xl">
                        Ready to test your knowledge? Pick an instant-answer practice quiz or challenge yourself with a timed assessment.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('dashboard', ['mode' => 'instant_answer']) }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-sm font-bold shadow-sm transition">
                        Instant Practice
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white text-sm font-semibold transition">
                        <span>★ Bookmarks</span>
                        <span class="px-2 py-0.5 rounded-full bg-white/20 text-xs font-bold">{{ $stats['bookmarks'] }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed</div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['completed'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Exams Passed</div>
                    <div class="text-2xl font-extrabold text-emerald-600">{{ $stats['passed'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg Score</div>
                    <div class="text-2xl font-extrabold text-violet-600">{{ $stats['avg_percentage'] }}%</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bookmarks</div>
                    <div class="text-2xl font-extrabold text-amber-600">{{ $stats['bookmarks'] }}</div>
                </div>
            </div>
        </div>

        {{-- In-progress attempts --}}
        @if($inProgressAttempts->isNotEmpty())
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-3">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">In-Progress Exam(s)</h3>
                </div>
                <div class="space-y-2.5">
                    @foreach($inProgressAttempts as $active)
                        <div class="flex flex-wrap items-center justify-between gap-3 bg-white rounded-xl px-4 py-3 border border-amber-200/70 shadow-xs">
                            <div>
                                <div class="font-bold text-slate-900">{{ $active->exam->title }}</div>
                                <div class="text-xs text-slate-500">Started {{ $active->started_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('attempts.take', $active) }}" class="px-5 py-2 rounded-xl bg-amber-600 text-white text-sm font-bold hover:bg-amber-700 transition shadow-xs">
                                Resume Now &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Quick Mode Tabs + Search & Filter --}}
        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('dashboard', ['category' => $categoryId ?: null, 'search' => $search ?: null]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $mode === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All Exams
                    </a>
                    <a href="{{ route('dashboard', ['mode' => 'instant_answer', 'category' => $categoryId ?: null, 'search' => $search ?: null]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $mode === 'instant_answer' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Instant Answer
                    </a>
                    <a href="{{ route('dashboard', ['mode' => 'standard', 'category' => $categoryId ?: null, 'search' => $search ?: null]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $mode === 'standard' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Timed Assessments
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="mode" value="{{ $mode }}">
                <div class="flex-1 min-w-[220px]">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search exam title or topic..." class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="category" class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId === $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition">Search</button>
                @if($search || $categoryId || $mode)
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-semibold text-slate-500 hover:text-slate-900">Reset</a>
                @endif
            </form>
        </div>

        {{-- Available Exams Grid --}}
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($exams as $exam)
                    <div class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-indigo-200 transition flex flex-col justify-between">
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
                                <h4 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $exam->title }}</h4>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $exam->description }}</p>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 space-y-4">
                                <div class="grid grid-cols-3 gap-2 text-center bg-slate-50 rounded-xl p-2.5 text-xs">
                                    <div>
                                        <div class="text-slate-400 font-medium">Questions</div>
                                        <div class="font-bold text-slate-800">{{ $exam->questions_count }}</div>
                                    </div>
                                    <div class="border-x border-slate-200">
                                        <div class="text-slate-400 font-medium">Duration</div>
                                        <div class="font-bold text-slate-800">{{ $exam->duration_minutes > 0 ? $exam->duration_minutes . 'm' : 'Untimed' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400 font-medium">Marks</div>
                                        <div class="font-bold text-slate-800">{{ $exam->total_marks }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('exams.show', $exam) }}" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition shadow-xs">
                                        Open Exam &rarr;
                                    </a>
                                    @if($exam->show_leaderboard)
                                        <a href="{{ route('exams.leaderboard', $exam) }}" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                            Ranks
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-2xl p-10 text-center text-slate-500 border border-slate-200">
                        No exams match your filters.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $exams->links() }}</div>
        </div>

        {{-- Recent Attempts --}}
        @if($recentAttempts->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="font-extrabold text-slate-900">Recent Exam History</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Exam</th>
                                <th class="px-6 py-3 text-left">Score</th>
                                <th class="px-6 py-3 text-left">Percentage</th>
                                <th class="px-6 py-3 text-left">Result</th>
                                <th class="px-6 py-3 text-left">Submitted</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentAttempts as $attempt)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $attempt->exam->title }}</td>
                                    <td class="px-6 py-3.5 font-medium">{{ $attempt->score }} / {{ $attempt->total_marks }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $attempt->is_passed ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ min(100, max(0, $attempt->percentage)) }}%"></div>
                                            </div>
                                            <span class="font-bold text-xs text-slate-700">{{ $attempt->percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $attempt->is_passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $attempt->is_passed ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500">{{ $attempt->submitted_at?->diffForHumans() }}</td>
                                    <td class="px-6 py-3.5 text-right space-x-3">
                                        <a href="{{ route('attempts.result', $attempt) }}" class="text-indigo-600 hover:underline font-semibold">Review</a>
                                        <a href="{{ route('attempts.certificate', $attempt) }}" class="text-slate-600 hover:underline font-medium">PDF</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
