<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_area_assignments', function (Blueprint $t) {
            $t->unsignedBigInteger('admin_id');
            $t->unsignedBigInteger('division_id');
            $t->unsignedBigInteger('district_id')->nullable();
            $t->string('area_name')->nullable();

            $t->timestamps();

            $t->unique(['admin_id', 'division_id', 'district_id', 'area_name'], 'area_unique');

            $t->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_area_assignments');
    }
};
