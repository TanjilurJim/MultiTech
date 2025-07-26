<?php

namespace App\Http\Controllers\Admin;

use App\Models\MonthlyFollowUpSummary;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Exports\MonthlySummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Admin\FollowUpReportController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;

class MonthlyFollowUpSummaryController extends Controller
{
    //
    public function index(Request $request)
    {
        $admin = auth('admin')->user();               // ← who is browsing?

        /* 1️⃣  Build query that respects visibility */
        $months = MonthlyFollowUpSummary::with('admin:id,name')
            ->visibleTo($admin)                       // ⬅️ scope we just added
            ->when($request->month,  fn($q) => $q->where('month',  $request->month))
            ->when($request->admin_id, fn($q) => $q->where('admin_id', $request->admin_id))
            ->orderByDesc('month')
            ->paginate(20);

        /* 2️⃣  Limit the admin-filter dropdown to rows this user can see */
        $admins = Admin::whereIn('id', $months->pluck('admin_id')->unique())
            ->select('id', 'name')->get();

        /* 3️⃣  Previous-month lookup (unchanged) */
        $prevMonths = [];
        foreach ($months as $m) {
            $prevKey = \Carbon\Carbon::parse($m->month . '-01')
                ->subMonth()->format('Y-m');
            $prevMonths[$m->admin_id][$m->month] =
                MonthlyFollowUpSummary::where('admin_id', $m->admin_id)
                ->where('month', $prevKey)
                ->value('contacted_total');
        }

        $pageTitle = 'Monthly Follow-Up Snapshots';
        return view(
            'admin.followups.summaries',
            compact('months', 'admins', 'pageTitle', 'prevMonths')
        );
    }

    public function updateNote(Request $request, MonthlyFollowUpSummary $summary)
    {
        $admin = auth('admin')->user();

        /* allow if: owner  OR  super-admin  OR  explicit edit permission */
        if (
            $summary->admin_id !== $admin->id &&
            !$admin->hasRole('super-admin') &&
            !$admin->can('followup_summaries.edit')
        ) {
            abort(403);
        }

        $request->validate(['summary_note' => 'nullable|string|max:5000']);
        $summary->update(['summary_note' => $request->summary_note]);

        return back()->withSuccess('Summary note updated.');
    }

    public function export(Request $request)
    {
        $admin = auth('admin')->user();

        $rows = MonthlyFollowUpSummary::with('admin:id,name')
            ->visibleTo($admin)
            ->when($request->month,  fn($q) => $q->where('month',  $request->month))
            ->when($request->admin_id, fn($q) => $q->where('admin_id', $request->admin_id))
            ->orderByDesc('month')
            ->get();

        return Excel::download(
            new MonthlySummaryExport($rows),
            'followup-snapshots-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
