<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-xl text-gray-900">Admin &amp; Examiner Dashboard</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">+ New Exam</a>
                <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800">+ New Question</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Students</div>
                <div class="mt-1 text-2xl font-bold">{{ $stats['students'] }}</div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Exams (Published)</div>
                <div class="mt-1 text-2xl font-bold">{{ $stats['exams'] }} <span class="text-sm font-normal text-emerald-600">({{ $stats['published_exams'] }})</span></div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Question Bank</div>
                <div class="mt-1 text-2xl font-bold">{{ $stats['questions'] }}</div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Attempts / Pass Rate</div>
                <div class="mt-1 text-2xl font-bold">{{ $stats['attempts'] }} <span class="text-sm font-normal text-indigo-600">({{ $stats['pass_rate'] }}%)</span></div>
            </div>
        </div>

        @if($stats['pending_reports'] > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between">
                <span class="text-sm text-amber-900 font-medium">You have {{ $stats['pending_reports'] }} pending question report(s) from students.</span>
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-bold text-amber-800 underline">Review Reports</a>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 font-bold text-gray-800">Recent Exam Submissions</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Student</th>
                            <th class="px-6 py-3 text-left">Exam</th>
                            <th class="px-6 py-3 text-left">Score</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAttempts as $att)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $att->user->name }}</td>
                                <td class="px-6 py-3">{{ $att->exam->title }}</td>
                                <td class="px-6 py-3">{{ $att->score }} / {{ $att->total_marks }} ({{ $att->percentage }}%)</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $att->is_passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $att->is_passed ? 'PASSED' : 'FAILED' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $att->submitted_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No submissions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

