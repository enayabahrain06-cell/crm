<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Used in URLs and QR codes
            $table->text('description')->nullable();
            $table->string('type')->default('restaurant'); // hotel, resort, bar, restaurant, club
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->default('Bahrain');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('timezone')->default('Asia/Bahrain');
            $table->string('currency')->default('BHD');
            $table->boolean('active')->default(true);
            $table->json('meta')->nullable(); // Additional settings
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outlet_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->string('role_at_outlet')->nullable(); // manager, staff, etc. within outlet
            $table->timestamps();
            $table->unique(['user_id', 'outlet_id']);
        });

        Schema::create('outlet_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // instagram, facebook, twitter, tiktok, etc.
            $table->string('label')->nullable(); // Button text
            $table->string('url');
            $table->string('icon')->nullable(); // Icon class
            $table->string('color')->nullable(); // Brand color
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['outlet_id', 'platform']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->string('description')->nullable();
            $table->string('event'); // created, updated, deleted, etc.
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable(); // Old/new values
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('event');
            $table->index('causer_id');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlet_user');
        Schema::dropIfExists('outlets');
    }
};

