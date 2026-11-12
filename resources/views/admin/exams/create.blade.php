<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Create New Exam</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <form method="POST" action="{{ route('admin.exams.store') }}" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Instructions</label>
                <textarea name="instructions" rows="3" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Mode</label>
                    <select name="mode" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="instant_answer">Instant Answer</option>
                        <option value="standard">Standard Timed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Duration (mins, 0=untimed)</label>
                    <input type="number" name="duration_minutes" value="30" min="0" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pass %</label>
                    <input type="number" step="0.5" name="pass_percentage" value="50" min="0" max="100" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Max Attempts (blank=unlimited)</label>
                    <input type="number" name="max_attempts" min="1" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-sm">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_published" value="1" checked class="rounded text-indigo-600"> Publish Immediately</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="lock_after_instant_reveal" value="1" checked class="rounded text-indigo-600"> Lock After Instant Reveal</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="allow_negative_marking" value="1" class="rounded text-indigo-600"> Enable Negative Marking</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="shuffle_questions" value="1" checked class="rounded text-indigo-600"> Shuffle Questions</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="shuffle_options" value="1" checked class="rounded text-indigo-600"> Shuffle Options</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="show_leaderboard" value="1" checked class="rounded text-indigo-600"> Show Leaderboard</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 rounded-lg border text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Create &amp; Assign Questions</button>
            </div>
        </form>
    </div>
</x-app-layout>

