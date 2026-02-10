<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->timestamp('visited_at')->useCurrent();
            $table->string('visit_type')->default('dine'); // stay, dine, bar, event, other
            $table->decimal('bill_amount', 10, 3)->default(0);
            $table->string('currency', 3)->default('BHD');
            $table->json('items_json')->nullable(); // [{name, category, quantity, unit_price}]
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable(); // Additional metadata
            $table->timestamps();

            $table->index('customer_id');
            $table->index('outlet_id');
            $table->index('visited_at');
            $table->index('visit_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

