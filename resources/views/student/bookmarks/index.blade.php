<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Bookmarked Questions for Revision</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-4">
        @forelse($bookmarks as $bookmark)
            @php $q = $bookmark->question; @endphp
            <div x-data="{ showAnswer: false }" class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $q->category->name ?? 'General' }} &bull; {{ ucfirst($q->difficulty) }}
                    </span>
                    <form method="POST" action="{{ route('questions.bookmark', $q) }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:underline">Remove Bookmark</button>
                    </form>
                </div>

                <p class="font-medium text-gray-900 whitespace-pre-line">{{ $q->question_text }}</p>

                <div class="mt-3 space-y-1.5">
                    @foreach($q->options as $opt)
                        <div class="px-3 py-2 rounded-lg border text-sm"
                             :class="showAnswer && {{ $opt->is_correct ? 'true' : 'false' }} ? 'border-emerald-400 bg-emerald-50 font-medium text-emerald-900' : 'border-gray-200'">
                            {{ $opt->option_text }}
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <button type="button" @click="showAnswer = !showAnswer" class="text-xs font-semibold text-indigo-600 hover:underline">
                        <span x-text="showAnswer ? 'Hide Answer & Explanation' : 'Reveal Answer & Explanation'"></span>
                    </button>
                </div>

                @if($q->explanation)
                    <div x-show="showAnswer" x-cloak class="mt-3 p-3 rounded-lg bg-indigo-50 text-sm text-gray-800">
                        <strong>Explanation:</strong> {{ $q->explanation }}
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                You haven't bookmarked any questions yet.
            </div>
        @endforelse

        <div>{{ $bookmarks->links() }}</div>
    </div>
</x-app-layout>

