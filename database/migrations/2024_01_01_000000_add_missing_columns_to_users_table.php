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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'active',
                'last_login_at',
                'last_login_ip',
                'phone',
                'avatar',
            ]);
        });
    }
};

