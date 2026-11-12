<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">Manage Users</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Attempts</th>
                        <th class="px-6 py-3 text-left">Role &amp; Status</th>
                        <th class="px-6 py-3 text-right">Save</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-3">{{ $user->attempts_count }}</td>
                            <td colspan="2" class="px-6 py-3">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center justify-end gap-3">
                                    @csrf @method('PATCH')
                                    <select name="role" class="rounded border-gray-300 text-xs">
                                        <option value="student" @selected($user->role === 'student')>Student</option>
                                        <option value="examiner" @selected($user->role === 'examiner')>Examiner</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                    <label class="inline-flex items-center gap-1 text-xs">
                                        <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="rounded text-indigo-600">
                                        Active
                                    </label>
                                    <button type="submit" class="px-3 py-1 rounded bg-indigo-600 text-white text-xs font-medium">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>

