<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-indigo-600">{{ $exam->category->name ?? 'General' }}</span>
                <h2 class="font-bold text-xl text-gray-900">{{ $exam->title }}</h2>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to Exams</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex flex-wrap gap-3 mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $exam->isInstantAnswerMode() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    Mode: {{ $exam->isInstantAnswerMode() ? 'Instant Answer + Explanation' : 'Standard Timed Assessment' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    Questions: {{ $exam->questions_count }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    Duration: {{ $exam->duration_minutes > 0 ? $exam->duration_minutes . ' minutes' : 'Untimed' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    Pass Mark: {{ $exam->pass_percentage }}%
                </span>
                @if($exam->allow_negative_marking)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        Negative Marking Enabled
                    </span>
                @endif
            </div>

            @if($exam->description)
                <p class="text-gray-700 mb-4">{{ $exam->description }}</p>
            @endif

            @if($exam->instructions)
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-2">Instructions</h3>
                    <div class="text-sm text-slate-700 whitespace-pre-line">{{ $exam->instructions }}</div>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @if($activeAttempt)
                    <a href="{{ route('attempts.take', $activeAttempt) }}" class="px-6 py-2.5 rounded-lg bg-amber-600 text-white font-semibold hover:bg-amber-700">
                        Resume In-Progress Attempt
                    </a>
                @elseif($canStart)
                    <form method="POST" action="{{ route('exams.start', $exam) }}">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                            Start Exam Now
                        </button>
                    </form>
                @else
                    <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-600 text-sm font-medium">
                        Exam unavailable or max attempts ({{ $exam->max_attempts }}) reached
                    </span>
                @endif

                @if($exam->show_leaderboard)
                    <a href="{{ route('exams.leaderboard', $exam) }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        View Leaderboard
                    </a>
                @endif
            </div>
        </div>

        @if($userAttempts->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">Your Previous Attempts</div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Score</th>
                            <th class="px-6 py-3 text-left">Percentage</th>
                            <th class="px-6 py-3 text-left">Result</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($userAttempts as $att)
                            <tr>
                                <td class="px-6 py-3 font-medium">Attempt #{{ $att->attempt_number }}</td>
                                <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $att->status) }}</td>
                                <td class="px-6 py-3">{{ $att->score }} / {{ $att->total_marks }}</td>
                                <td class="px-6 py-3">{{ $att->percentage }}%</td>
                                <td class="px-6 py-3">
                                    @if($att->status !== 'in_progress')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $att->is_passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $att->is_passed ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    @else
                                        <span class="text-amber-600 text-xs font-semibold">IN PROGRESS</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right space-x-3">
                                    @if($att->status === 'in_progress')
                                        <a href="{{ route('attempts.take', $att) }}" class="text-amber-600 font-semibold hover:underline">Resume</a>
                                    @else
                                        <a href="{{ route('attempts.result', $att) }}" class="text-indigo-600 font-semibold hover:underline">Review</a>
                                        <a href="{{ route('attempts.certificate', $att) }}" class="text-gray-600 hover:underline">PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>

