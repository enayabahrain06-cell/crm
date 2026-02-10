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
        // Make country nullable for outlets table
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('country')->nullable()->default('Bahrain')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert country back to NOT NULL
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('country')->default('Bahrain')->change();
        });
    }
};

