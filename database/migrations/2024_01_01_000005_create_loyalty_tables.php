<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->integer('points_expired')->default(0);
            $table->string('tier')->nullable(); // basic, silver, gold, platinum
            $table->timestamp('last_earned_at')->nullable();
            $table->timestamp('last_redeemed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('customer_id');
            $table->index('total_points');
        });

        Schema::create('loyalty_point_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->string('source_type'); // visit, manual_adjustment, campaign, expiry, correction, reward_redemption
            $table->unsignedBigInteger('source_id')->nullable();
            $table->integer('points'); // Positive for earn, negative for redeem/expire
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable(); // Rule ID, calculation details, etc.
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('outlet_id');
            $table->index('source_type');
            $table->index('created_at');
        });

        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('earn'); // earn, burn
            $table->boolean('active')->default(true);
            $table->json('condition_json')->nullable(); // {outlet_ids, visit_type, min_spend, date_ranges, first_visit, nth_visit, birthday_visit, etc.}
            $table->json('formula_json')->nullable(); // {fixed_points, points_per_amount, caps, multipliers}
            $table->integer('priority')->default(0); // Lower number = higher priority
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('active');
            $table->index('type');
            $table->index('priority');
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('required_points');
            $table->string('type')->default('voucher'); // stay, drink, voucher, discount, gift, other
            $table->json('outlet_scope_json')->nullable(); // Array of outlet IDs or "all"
            $table->decimal('discount_value', 10, 3)->nullable(); // For voucher rewards
            $table->string('currency', 3)->default('BHD');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('max_redemptions')->nullable(); // Null = unlimited
            $table->integer('current_redemptions')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('active');
            $table->index('type');
        });

        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->integer('points_redeemed');
            $table->string('redemption_code')->unique()->nullable(); // Unique code for the redemption
            $table->enum('status', ['pending', 'completed', 'cancelled', 'expired'])->default('pending');
            $table->foreignId('redeemed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('status');
            $table->index('redemption_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('loyalty_point_ledger');
        Schema::dropIfExists('loyalty_wallets');
        Schema::dropIfExists('loyalty_rules');
    }
};

