<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasAreaScope
{
    /**
     * Limit query to customers in areas assigned to the given admin.
     *
     * Level of restriction:
     * 1. If only division_id is set – whole division.
     * 2. If district_id is also set – that district only.
     * 3. If area_name is also set – that specific area/thana only.
     */
    public function scopeVisibleTo(Builder $query, $admin)
    {
        return $query->whereExists(function ($q) use ($admin) {
            $q->selectRaw(1)
              ->from('admin_area_assignments as a')
              // match this admin
              ->where('a.admin_id', $admin->id)
              // must match division
              ->whereColumn('a.division_id', 'customers.division_id')
              // district rule: if assignment row has district_id, require match; otherwise ignore
              ->where(function ($d) {
                  $d->whereNull('a.district_id')
                    ->orWhereColumn('a.district_id', 'customers.district_id');
              })
              // area rule: if assignment row has area_name, require match; otherwise ignore
              ->where(function ($a) {
                  $a->whereNull('a.area_name')
                    ->orWhereColumn('a.area_name', 'customers.area_name');
              });
        });
    }
}
