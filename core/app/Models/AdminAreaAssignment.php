<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAreaAssignment extends Model
{
    protected $fillable = [
        'admin_id',
        'division_id',
        'district_id',
        'area_name',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}