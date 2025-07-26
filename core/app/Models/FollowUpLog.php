<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Appp\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Admin;


class FollowUpLog extends Model
{
    //
    protected $fillable = [
        'user_id',
        'contact_date',
        'admin_id',
        'customers_contacted',
        'potential_customers',
        'notes',
        'division_id',
        'district_id',
        'thana_id',
    ];

    protected $casts = ['contact_date' => 'date'];

    /* Relationships */
     public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /** Master visibility rule */
    public function scopeVisibleTo(Builder $query, $admin): Builder
    {
        // 1. super-admin sees everything
        if ($admin->hasRole('super-admin')) {
            return $query;
        }

        // 2. optional: “view all follow-ups” permission
        if ($admin->can('followup_logs.view_all')) {
            return $query;
        }

        // 3. fallback: only logs they created
        return $query->where('admin_id', $admin->id);
    }
}
