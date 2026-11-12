<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('attempts')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,examiner,student'],
            'is_active' => ['boolean'],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->update([
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'User updated.');
    }
}

