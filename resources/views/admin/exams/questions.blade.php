<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900">Assign Questions: {{ $exam->title }}</h2>
                <p class="text-xs text-gray-500">Currently attached: {{ count($attachedIds) }} questions &bull; Total Marks: {{ $exam->total_marks }}</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Exams</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-5">
        <form method="GET" action="{{ route('admin.exams.questions', $exam) }}" class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter questions..." class="flex-1 rounded-lg border-gray-300 text-sm">
            <select name="category_id" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium">Filter</button>
        </form>

        <form method="POST" action="{{ route('admin.exams.questions.sync', $exam) }}" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @csrf
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <span class="text-sm font-semibold text-gray-700">Check questions to include in this exam</span>
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                    Save Question Assignment
                </button>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($availableQuestions as $q)
                    <label class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" @checked(in_array($q->id, $attachedIds, true)) class="mt-1 rounded text-indigo-600">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $q->question_text }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $q->category->name ?? 'General' }} &bull; {{ ucfirst($q->difficulty) }} &bull; {{ $q->marks }} marks
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </form>

        <div>{{ $availableQuestions->links() }}</div>
    </div>
</x-app-layout>

