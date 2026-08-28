<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->index(['status', 'created_at']);
            $table->index(['provider', 'external_id']);
        });

        Schema::table('shipments', function (Blueprint $table): void {
            $table->index(['status', 'created_at']);
            $table->index('tracking_code');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['tracking_code']);
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['provider', 'external_id']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
