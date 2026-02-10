<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('individual'); // individual, corporate
            $table->string('name');
            $table->string('nationality')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unknown'])->default('unknown');
            $table->string('email')->nullable();
            $table->json('mobile_json')->nullable(); // {country_iso2, country_dial_code, national_number, e164}
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('company_name')->nullable(); // For corporate customers
            $table->string('position')->nullable(); // For corporate customers
            $table->foreignId('first_registration_outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->json('preferences')->nullable(); // Communication preferences, etc.
            $table->json('meta')->nullable(); // Additional metadata
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index('nationality');
            $table->index('gender');
            $table->index('date_of_birth');
            $table->index('first_registration_outlet_id');
            $table->index('created_at');
            $table->unique(['email'], 'customers_email_unique');
        });

        // Add unique index on normalized mobile E.164
        // For SQLite compatibility, use a regular column (virtual columns have issues with SQLite)
        Schema::table('customers', function (Blueprint $table) {
            $table->string('mobile_e164')->nullable()->index();
        });

        Schema::create('customer_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->default('#007bff'); // For UI display
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('customer_tags')->onDelete('cascade');
            $table->string('tagged_by')->nullable(); // User who tagged
            $table->timestamps();
            $table->unique(['customer_id', 'tag_id']);
        });

        Schema::create('customer_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->string('event_type'); // registration, first_visit, birthday_visit, etc.
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('customer_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_events');
        Schema::dropIfExists('customer_tag_pivot');
        Schema::dropIfExists('customer_tags');
        Schema::dropIfExists('customers');
    }
};

