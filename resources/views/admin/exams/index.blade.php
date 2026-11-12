<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900">Manage Exams</h2>
            <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">+ Create Exam</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Mode</th>
                        <th class="px-6 py-3 text-left">Questions / Marks</th>
                        <th class="px-6 py-3 text-left">Attempts</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($exams as $exam)
                        <tr>
                            <td class="px-6 py-3">
                                <div class="font-bold text-gray-900">{{ $exam->title }}</div>
                                <div class="text-xs text-gray-500">{{ $exam->category->name ?? 'General' }} &bull; {{ $exam->duration_minutes }} mins</div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $exam->isInstantAnswerMode() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $exam->isInstantAnswerMode() ? 'Instant Answer' : 'Standard' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $exam->questions_count }} Qs / {{ $exam->total_marks }} Marks</td>
                            <td class="px-6 py-3">{{ $exam->attempts_count }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $exam->is_published ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $exam->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('admin.exams.questions', $exam) }}" class="text-emerald-600 hover:underline font-medium">Questions</a>
                                <a href="{{ route('admin.exams.results', $exam) }}" class="text-indigo-600 hover:underline font-medium">Results</a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="text-gray-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" class="inline" onsubmit="return confirm('Delete this exam?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $exams->links() }}</div>
    </div>
</x-app-layout>

