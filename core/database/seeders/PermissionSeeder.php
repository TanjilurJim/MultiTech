<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules   = [
            'customers',
            'followup_logs',
            'followup_reports',
            'followup_summaries',
            // -- add any future sidebar items here
        ];

        $abilities = ['add', 'view', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($abilities as $ability) {
                Permission::firstOrCreate(
                    ['name' => "$module.$ability", 'guard_name' => 'admin']
                );
            }
        }
    }
}
