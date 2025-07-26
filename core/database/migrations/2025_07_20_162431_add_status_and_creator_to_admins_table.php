<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // ① enable / disable
            $table->boolean('is_active')->default(true)->after('image');   // or after password

            // ② who created this admin?
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')        // references id on admins
                ->nullOnDelete()
                ->after('is_active');

            // (optional) quick index to speed up “active users” scope
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn('is_active');
        });
    }
};
