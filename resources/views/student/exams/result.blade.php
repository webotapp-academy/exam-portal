<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">Exam Result: {{ $attempt->exam->title }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Verification ID: <span class="font-mono font-bold text-slate-700">{{ $attempt->verificationCode() }}</span></p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('attempts.certificate', $attempt) }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-xs transition">
                    Download PDF Scorecard
                </a>
                @if($attempt->exam->show_leaderboard)
                    <a href="{{ route('exams.leaderboard', $attempt->exam) }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Leaderboard
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        {{-- Result Hero Card --}}
        <div class="rounded-2xl p-6 sm:p-8 text-white shadow-lg {{ $attempt->is_passed ? 'bg-gradient-to-br from-emerald-600 to-teal-700' : 'bg-gradient-to-br from-slate-800 to-slate-900' }}">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-white/20">
                        {{ $attempt->is_passed ? '✓ Passed' : '✕ Not Passed' }}
                    </span>
                    <h3 class="mt-3 text-3xl font-extrabold">{{ $attempt->percentage }}% Score</h3>
                    <p class="text-sm text-white/80 mt-1">
                        You scored {{ $attempt->score }} out of {{ $attempt->total_marks }} marks in {{ $attempt->durationFormatted() }}.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-xl bg-white/15 backdrop-blur px-4 py-3">
                        <div class="text-2xl font-extrabold">{{ $attempt->correct_count }}</div>
                        <div class="text-[11px] uppercase tracking-wider text-white/80">Correct</div>
                    </div>
                    <div class="rounded-xl bg-white/15 backdrop-blur px-4 py-3">
                        <div class="text-2xl font-extrabold">{{ $attempt->wrong_count }}</div>
                        <div class="text-[11px] uppercase tracking-wider text-white/80">Wrong</div>
                    </div>
                    <div class="rounded-xl bg-white/15 backdrop-blur px-4 py-3">
                        <div class="text-2xl font-extrabold">{{ $attempt->unanswered_count }}</div>
                        <div class="text-[11px] uppercase tracking-wider text-white/80">Skipped</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Question-by-Question Review --}}
        <div class="space-y-4">
            <h3 class="text-lg font-extrabold text-slate-900">Detailed Answer Review</h3>
            @foreach($reviewItems as $item)
                @php
                    $q = $item['question'];
                    $ans = $item['answer'];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs">Q{{ $item['number'] }}</span>
                            @if($ans === null || empty($item['selected_ids']))
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">Unanswered</span>
                            @elseif($ans->is_correct)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">✓ Correct (+{{ $ans->marks_awarded }})</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">✕ Wrong ({{ $ans->marks_awarded }})</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('questions.bookmark', $q) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-lg border text-xs font-semibold {{ $item['is_bookmarked'] ? 'bg-amber-50 border-amber-200 text-amber-700' : 'border-slate-200 text-slate-500 hover:text-indigo-600' }}">
                                {{ $item['is_bookmarked'] ? '★ Bookmarked' : '☆ Bookmark' }}
                            </button>
                        </form>
                    </div>

                    <p class="font-semibold text-slate-900 mb-4 whitespace-pre-line">{{ $q->question_text }}</p>

                    <div class="space-y-2">
                        @foreach($q->options as $opt)
                            @php
                                $isSelected = in_array($opt->id, $item['selected_ids'], true);
                                $isCorrect = in_array($opt->id, $item['correct_ids'], true);
                                $cls = 'border-slate-200 bg-white';
                                if ($isCorrect) $cls = 'border-emerald-400 bg-emerald-50/70';
                                elseif ($isSelected && !$isCorrect) $cls = 'border-red-400 bg-red-50/70';
                            @endphp
                            <div class="px-4 py-3 rounded-xl border text-sm flex items-center justify-between {{ $cls }}">
                                <span class="font-medium text-slate-800">{{ $opt->option_text }}</span>
                                <span class="text-xs font-bold">
                                    @if($isCorrect && $isSelected)
                                        <span class="text-emerald-700">✓ Your Answer</span>
                                    @elseif($isCorrect)
                                        <span class="text-emerald-700">✓ Correct Answer</span>
                                    @elseif($isSelected)
                                        <span class="text-red-700">✕ Your Answer</span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($q->explanation)
                        <div class="mt-4 p-4 rounded-xl bg-indigo-50/70 border border-indigo-100 text-sm text-slate-800">
                            <span class="font-bold text-indigo-800">Explanation:</span> {{ $q->explanation }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
