<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Reported Questions</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Question</th>
                        <th class="px-6 py-3 text-left">Reported By</th>
                        <th class="px-6 py-3 text-left">Reason &amp; Details</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-6 py-3 max-w-xs">
                                <a href="{{ route('admin.questions.edit', $report->question) }}" class="text-indigo-600 hover:underline font-medium line-clamp-2">
                                    {{ $report->question->question_text }}
                                </a>
                            </td>
                            <td class="px-6 py-3">{{ $report->user->name }}</td>
                            <td class="px-6 py-3">
                                <div class="font-medium">{{ $report->reason }}</div>
                                <div class="text-xs text-gray-500">{{ $report->details }}</div>
                            </td>
                            <td class="px-6 py-3 capitalize">{{ $report->status }}</td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="inline-flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" class="rounded border-gray-300 text-xs">
                                        <option value="pending" @selected($report->status === 'pending')>Pending</option>
                                        <option value="resolved" @selected($report->status === 'resolved')>Resolved</option>
                                        <option value="dismissed" @selected($report->status === 'dismissed')>Dismissed</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1 rounded bg-gray-900 text-white text-xs">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reports->links() }}</div>
    </div>
</x-app-layout>

