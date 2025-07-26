<?php

namespace App\Models;

use App\Models\AdminAreaAssignment;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;

    /** Every role / permission this model touches belongs to the `admin` guard */
    protected string $guard_name = 'admin';

    /** Allow mass-assignment for these columns (choose ONE of the two blocks) */
    // --- Option A: explicit list (recommended) -------------
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'image',
        'is_active',
        'created_by',         // add others if you ever save them via create()/update()
    ];

    // --- Option B: open everything --------------------------
    // protected $guarded = [];

    /** Hide sensitive columns when the model is serialized */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Auto–hash when we set `password` */
    protected function setPasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /* Relationships */
    public function followUpLogs()
    {
        return $this->hasMany(\App\Models\FollowUpLog::class);
    }

    public function areaAssignments()
    {
        return $this->hasMany(AdminAreaAssignment::class, 'admin_id');
    }
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* who created this admin */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    
}
