<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FollowUpLog;
use App\Exports\MonthlyReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FollowUpReportController extends Controller
{
    public function monthly(Request $request)
    {
        $admin      = auth('admin')->user();
        $rangeStart = now()->subDays(30)->startOfDay();

        /* -----------------------------------------------------------------
           1. Build base query that respects visibility
        ----------------------------------------------------------------- */
        $base = FollowUpLog::visibleTo($admin)          // super-admin → all logs
            ->where('contact_date', '>=', $rangeStart)
            ->with('admin:id,name');

        /* -----------------------------------------------------------------
           2. Company-wide (or scope-wide) totals
        ----------------------------------------------------------------- */
        $stats = (clone $base)->selectRaw('
                     SUM(customers_contacted)  AS contacted,
                     SUM(potential_customers)  AS potential
                 ')->first();

        /* -----------------------------------------------------------------
           3. Per-admin breakdown
                 – If super-admin or view_all → might include many admins
                 – Else   → probably just one (themselves)
        ----------------------------------------------------------------- */
        $summaries = (clone $base)->groupBy('admin_id')
            ->select('admin_id')
            ->selectRaw('SUM(customers_contacted) contacted,
                         SUM(potential_customers) potential')
            ->with('admin:id,name')
            ->get();

        /* -----------------------------------------------------------------
           4. Excel download?  Same subset
        ----------------------------------------------------------------- */
        if ($request->boolean('download')) {
            return Excel::download(
                new MonthlyReportExport($summaries, $stats),
                'follow-up-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        $pageTitle = '30-Day Follow-Up Report';
        return view('admin.followups.report', compact('stats', 'summaries', 'pageTitle'));
    }
}
