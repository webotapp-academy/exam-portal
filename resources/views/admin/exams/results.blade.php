<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900">Exam Results: {{ $exam->title }}</h2>
            <a href="{{ route('admin.exams.results.export', $exam) }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Student</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Score</th>
                        <th class="px-6 py-3 text-left">%</th>
                        <th class="px-6 py-3 text-left">Result</th>
                        <th class="px-6 py-3 text-left">Tab Switches</th>
                        <th class="px-6 py-3 text-right">Review</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $att)
                        <tr>
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $att->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $att->user->email }}</div>
                            </td>
                            <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $att->status) }}</td>
                            <td class="px-6 py-3">{{ $att->score }} / {{ $att->total_marks }}</td>
                            <td class="px-6 py-3">{{ $att->percentage }}%</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $att->is_passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $att->is_passed ? 'PASSED' : 'FAILED' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $att->tab_switch_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('attempts.result', $att) }}" class="text-indigo-600 hover:underline font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No attempts recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $attempts->links() }}</div>
    </div>
</x-app-layout>

