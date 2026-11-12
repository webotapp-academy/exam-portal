<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Edit MCQ Question</h2>
    </x-slot>

    @php
        $existingOptions = $question->options->map(fn($o) => ['text' => $o->option_text, 'correct' => $o->is_correct])->values()->all();
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <form method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data"
              x-data="{ options: @js($existingOptions), addOption() { this.options.push({text: '', correct: false}); }, removeOption(i) { if (this.options.length > 2) this.options.splice(i, 1); } }"
              class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($question->category_id === $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Question Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="single_choice" @selected($question->type === 'single_choice')>Single Choice</option>
                        <option value="multiple_choice" @selected($question->type === 'multiple_choice')>Multiple Choice</option>
                        <option value="true_false" @selected($question->type === 'true_false')>True / False</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Difficulty</label>
                    <select name="difficulty" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="easy" @selected($question->difficulty === 'easy')>Easy</option>
                        <option value="medium" @selected($question->difficulty === 'medium')>Medium</option>
                        <option value="hard" @selected($question->difficulty === 'hard')>Hard</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Question Text</label>
                <textarea name="question_text" rows="3" required class="w-full rounded-lg border-gray-300 text-sm">{{ $question->question_text }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Marks</label>
                    <input type="number" step="0.25" name="marks" value="{{ $question->marks }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Negative Marks</label>
                    <input type="number" step="0.25" name="negative_marks" value="{{ $question->negative_marks }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked($question->is_active) class="rounded text-indigo-600">
                        Active
                    </label>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold uppercase text-gray-700">Answer Options</label>
                    <button type="button" @click="addOption()" class="text-xs font-semibold text-indigo-600 hover:underline">+ Add Option</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(opt, idx) in options" :key="idx">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="correct_options[]" :value="idx" x-model="opt.correct" class="rounded text-emerald-600">
                            <input type="text" :name="`options[${idx}]`" x-model="opt.text" required class="flex-1 rounded-lg border-gray-300 text-sm">
                            <button type="button" @click="removeOption(idx)" x-show="options.length > 2" class="text-xs text-red-500 hover:underline">Remove</button>
                        </div>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Explanation</label>
                <textarea name="explanation" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ $question->explanation }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t">
                <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 rounded-lg border text-sm">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Update Question</button>
            </div>
        </form>
    </div>
</x-app-layout>

