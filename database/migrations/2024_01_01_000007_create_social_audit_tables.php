<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if tables already exist (for SQLite compatibility)
        $outletSocialLinksExists = DB::table('sqlite_master')
            ->where('type', 'table')
            ->where('name', 'outlet_social_links')
            ->exists();

        $auditLogsExists = DB::table('sqlite_master')
            ->where('type', 'table')
            ->where('name', 'audit_logs')
            ->exists();

        if (!$outletSocialLinksExists) {
            Schema::create('outlet_social_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
                $table->string('platform'); // instagram, facebook, tiktok, snapchat, whatsapp, website, email, other
                $table->string('label')->nullable(); // Button text (e.g., "Follow us on Instagram")
                $table->string('url');
                $table->string('icon')->nullable(); // Icon class or path
                $table->string('color')->nullable(); // Brand color for button
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('outlet_id');
                $table->index('is_active');
                $table->index('sort_order');
                $table->unique(['outlet_id', 'platform']);
            });
        }

        if (!$auditLogsExists) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action'); // created, updated, deleted, exported, etc.
                $table->string('entity_type'); // Customer, Visit, Outlet, Campaign, etc.
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('old_values')->nullable(); // For updates/deletes
                $table->json('new_values')->nullable(); // For creates/updates
                $table->json('meta')->nullable(); // Additional context
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('entity_type');
                $table->index('entity_id');
                $table->index('action');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('outlet_social_links');
    }
};

