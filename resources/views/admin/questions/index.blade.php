<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-xl text-gray-900">Question Bank</h2>
            <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">+ Add Question</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-5">
        {{-- Bulk CSV Import --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <form method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <label class="text-sm font-semibold text-gray-700">Bulk CSV Import:</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required class="text-sm text-gray-600">
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">Import CSV</button>
            </form>
            <a href="{{ route('admin.questions.sample-csv') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Download Sample CSV Template</a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.questions.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search question text..." class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <select name="category_id" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="difficulty" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Difficulties</option>
                <option value="easy" @selected(request('difficulty') === 'easy')>Easy</option>
                <option value="medium" @selected(request('difficulty') === 'medium')>Medium</option>
                <option value="hard" @selected(request('difficulty') === 'hard')>Hard</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium">Filter</button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Question</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-left">Type / Difficulty</th>
                        <th class="px-6 py-3 text-left">Marks</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($questions as $q)
                        <tr>
                            <td class="px-6 py-3 max-w-md">
                                <div class="font-medium text-gray-900 line-clamp-2">{{ $q->question_text }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $q->options->count() }} options</div>
                            </td>
                            <td class="px-6 py-3">{{ $q->category->name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-xs">{{ str_replace('_', ' ', $q->type) }}</span>
                                <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-xs">{{ $q->difficulty }}</span>
                            </td>
                            <td class="px-6 py-3">+{{ $q->marks }} / -{{ $q->negative_marks }}</td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('admin.questions.edit', $q) }}" class="text-indigo-600 hover:underline font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" class="inline" onsubmit="return confirm('Delete this question?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No questions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $questions->links() }}</div>
    </div>
</x-app-layout>

