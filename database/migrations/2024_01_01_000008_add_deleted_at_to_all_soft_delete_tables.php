<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'visits',
            'campaigns',
            'campaign_messages',
            'loyalty_wallets',
            'loyalty_point_ledgers',
            'loyalty_rules',
            'rewards',
            'reward_redemptions',
            'outlets',
            'auto_greeting_rules',
            'auto_greeting_logs',
            'customer_tags',
            'customer_events',
            'outlet_social_links',
            'audit_logs',
        ];

        foreach ($tables as $table) {
            // Check if table exists
            $tableExists = DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', $table)
                ->exists();

            if (!$tableExists) {
                continue;
            }

            // Check if column already exists
            $hasColumn = DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', $table)
                ->whereRaw('sql LIKE "%deleted_at%"')
                ->exists();

            if (!$hasColumn) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'visits',
            'campaigns',
            'campaign_messages',
            'loyalty_wallets',
            'loyalty_point_ledgers',
            'loyalty_rules',
            'rewards',
            'reward_redemptions',
            'outlets',
            'auto_greeting_rules',
            'auto_greeting_logs',
            'customer_tags',
            'customer_events',
            'outlet_social_links',
            'audit_logs',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

