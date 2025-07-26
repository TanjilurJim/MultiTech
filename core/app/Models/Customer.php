<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasAreaScope;

class Customer extends Model
{
    /**
     * Bring in the original scope from the trait *under a new name*.
     * PHP lets you alias trait methods like this:
     */
    use HasAreaScope {
        HasAreaScope::scopeVisibleTo as protected scopeApplyArea;   // ⬅️ alias
    }



    protected $fillable = [
        'name',
        'company',
        'contact_number',
        'email',
        'division_id',
        'district_id',
        'thana_id',
        'area_name',
        'postcode',
        'remarks',
        'customer_type',
        'created_by',
    ];

    /**
     * Master scope: combines
     *   • super-admin bypass
     *   • customers.view_all bypass
     *   • otherwise the original area filter
     */
    public function scopeVisibleTo(Builder $query, $admin): Builder
    {
        if (
            $admin->hasRole('super-admin') ||
            $admin->can('customers.view_all')
        ) {
            return $query;               // show everything
        }

        // fall back to the trait’s area-based filter
        return $this->scopeApplyArea($query, $admin);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }
}
