<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900">Leaderboard: {{ $exam->title }}</h2>
            <a href="{{ route('exams.show', $exam) }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Exam</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Rank</th>
                        <th class="px-6 py-3 text-left">Student</th>
                        <th class="px-6 py-3 text-left">Score</th>
                        <th class="px-6 py-3 text-left">Percentage</th>
                        <th class="px-6 py-3 text-left">Duration</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topAttempts as $idx => $att)
                        <tr class="{{ $att->user_id === auth()->id() ? 'bg-indigo-50/50 font-semibold' : '' }}">
                            <td class="px-6 py-3 font-bold">#{{ $idx + 1 }}</td>
                            <td class="px-6 py-3">{{ $att->user->name }}</td>
                            <td class="px-6 py-3">{{ $att->score }} / {{ $att->total_marks }}</td>
                            <td class="px-6 py-3">{{ $att->percentage }}%</td>
                            <td class="px-6 py-3">{{ $att->durationFormatted() }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $att->submitted_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No completed attempts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

