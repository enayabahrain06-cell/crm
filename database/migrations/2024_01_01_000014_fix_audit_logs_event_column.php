<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The audit_logs table was created with event column as NOT NULL without default
        // This migration adds a default value to the event column
        Schema::table('audit_logs', function (Blueprint $table) {
            // Change event column to nullable with default, then back to not null with default
            // SQLite doesn't support altering column directly, so we use raw SQL
            DB::statement("ALTER TABLE audit_logs ALTER COLUMN event VARCHAR DEFAULT 'action'");
        });
        
        // Alternative approach: Add the column with proper default using raw SQL
        DB::statement("UPDATE audit_logs SET event = action WHERE event IS NULL");
    }

    public function down(): void
    {
        // No need to revert this change
    }
};

