<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Create MCQ Question</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data"
              x-data="{ options: ['', '', '', ''], addOption() { this.options.push(''); }, removeOption(i) { if (this.options.length > 2) this.options.splice(i, 1); } }"
              class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Question Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="single_choice">Single Choice</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True / False</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Difficulty</label>
                    <select name="difficulty" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Question Text</label>
                <textarea name="question_text" rows="3" required class="w-full rounded-lg border-gray-300 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Marks</label>
                    <input type="number" step="0.25" name="marks" value="1" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Negative Marks</label>
                    <input type="number" step="0.25" name="negative_marks" value="0" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Image (optional)</label>
                    <input type="file" name="question_image" accept="image/*" class="w-full text-xs text-gray-600">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold uppercase text-gray-700">Answer Options (Check the correct one(s))</label>
                    <button type="button" @click="addOption()" class="text-xs font-semibold text-indigo-600 hover:underline">+ Add Option</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(opt, idx) in options" :key="idx">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="correct_options[]" :value="idx" class="rounded text-emerald-600 focus:ring-emerald-500" title="Mark as correct">
                            <input type="text" :name="`options[${idx}]`" x-model="options[idx]" required placeholder="Option text..." class="flex-1 rounded-lg border-gray-300 text-sm">
                            <button type="button" @click="removeOption(idx)" x-show="options.length > 2" class="text-xs text-red-500 hover:underline">Remove</button>
                        </div>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Answer Explanation (Shown in Instant Answer Mode &amp; Review)</label>
                <textarea name="explanation" rows="2" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Explain why the correct answer is right..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t">
                <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 rounded-lg border text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Save Question</button>
            </div>
        </form>
    </div>
</x-app-layout>

