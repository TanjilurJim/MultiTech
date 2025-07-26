<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlySummaryExport implements FromCollection, WithHeadings
{
    protected Collection $rows;

    /** @param \Illuminate\Support\Collection $rows  already filtered & eager-loaded */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows->map(function ($row) {
            return [
                'Month'             => $row->month,
                'Employee'          => $row->admin->name,
                'Customers Contacted' => $row->contacted_total,
                'Potential Customers' => $row->potential_total,
                'Note'              => $row->summary_note,
            ];
        });
    }

    public function headings(): array
    {
        return array_keys($this->collection()->first() ?? []);
    }
}
