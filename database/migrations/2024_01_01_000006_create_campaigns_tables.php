<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('channel')->default('email'); // email, sms, push, etc.
            $table->string('status')->default('draft'); // draft, scheduled, sending, completed, cancelled
            $table->json('segment_definition_json')->nullable(); // Filters for customer segmentation
            $table->text('subject')->nullable();
            $table->longText('body')->nullable(); // HTML content
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('channel');
            $table->index('scheduled_at');
        });

        Schema::create('campaign_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('status')->default('queued'); // queued, sending, sent, failed, opened, clicked
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->text('tracking_token')->nullable(); // For open/click tracking
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('tracking_token');
        });

        Schema::create('auto_greeting_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Birthday Greeting", "Bahrain National Day"
            $table->string('trigger_type'); // birthday, fixed_date
            $table->string('trigger_date')->nullable(); // For fixed dates (MM-DD format)
            $table->string('nationality_filter')->nullable(); // e.g., "BH" for Bahraini only
            $table->string('channel')->default('email');
            $table->text('template_subject')->nullable();
            $table->longText('template_body')->nullable(); // HTML with placeholders
            $table->boolean('active')->default(true);
            $table->integer('days_before')->nullable(); // For birthday: send X days before
            $table->string('time')->default('09:00'); // Time to send
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('active');
            $table->index('trigger_type');
        });

        Schema::create('auto_greeting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('channel')->default('email');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('tracking_token')->nullable();
            $table->timestamps();

            $table->index('rule_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_greeting_logs');
        Schema::dropIfExists('auto_greeting_rules');
        Schema::dropIfExists('campaign_messages');
        Schema::dropIfExists('campaigns');
    }
};

