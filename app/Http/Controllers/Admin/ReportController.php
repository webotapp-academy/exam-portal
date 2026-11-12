<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = QuestionReport::with(['user', 'question'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, QuestionReport $report): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,resolved,dismissed'],
        ]);

        $report->update(['status' => $data['status']]);

        return back()->with('success', 'Report status updated.');
    }
}

