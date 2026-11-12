<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Categories &amp; Subjects</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Category Name</label>
                <input type="text" name="name" required class="w-full rounded-lg border-gray-300 text-sm" placeholder="e.g. Data Structures">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Optional short description">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                Add Category
            </button>
        </form>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Exams</th>
                        <th class="px-6 py-3 text-left">Questions</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($categories as $cat)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $cat->name }}
                                <div class="text-xs text-gray-500">{{ $cat->description }}</div>
                            </td>
                            <td class="px-6 py-3">{{ $cat->exams_count }}</td>
                            <td class="px-6 py-3">{{ $cat->questions_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $categories->links() }}</div>
    </div>
</x-app-layout>

