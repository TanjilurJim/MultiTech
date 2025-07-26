<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MonthlyFollowUpSummary extends Model
{
    protected $fillable = [
        'month',
        'admin_id',
        'contacted_total',
        'potential_total',
        'summary_note',
    ];

    /* -------------------------------------------------
       Relationships
    ------------------------------------------------- */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /* -------------------------------------------------
       Visibility scope
         • super-admin           → sees everything
         • followup_summaries.view_all  → sees everything
         • otherwise             → only their own rows
    ------------------------------------------------- */
    public function scopeVisibleTo(Builder $q, $admin): Builder
    {
        if (
            $admin->hasRole('super-admin') ||
            $admin->can('followup_summaries.view_all')   // <- add this in seeder if you want it
        ) {
            return $q;   // no filtering
        }

        return $q->where('admin_id', $admin->id);
    }
}
