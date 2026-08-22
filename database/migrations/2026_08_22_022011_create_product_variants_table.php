<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();

            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();

            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reserved_stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);

            $table->json('attributes')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'product_id',
                'is_active',
            ]);

            $table->index([
                'stock',
                'low_stock_threshold',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
