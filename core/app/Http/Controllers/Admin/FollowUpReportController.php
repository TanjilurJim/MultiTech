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
        $rangeStart = now()->subDays(30)->startOfDay();

        $base = FollowUpLog::where('contact_date', '>=', $rangeStart);

        if (!auth()->user()->hasRole('admin')) {
            $base->where('user_id', auth()->id());
        }

        $stats = (clone $base)->selectRaw('
                    SUM(customers_contacted) as contacted,
                    SUM(potential_customers) as potential
                 ')->first();

        $summaries = (clone $base)->groupBy('user_id')
            ->select('user_id')
            ->selectRaw('SUM(customers_contacted) contacted, SUM(potential_customers) potential')
            ->with('user:id,name')
            ->get();

        /* excel download */
        if ($request->boolean('download')) {
            return Excel::download(
                new MonthlyReportExport($summaries),
                'follow-up-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        return view('admin.followups.report', compact('stats', 'summaries'));
    }
}