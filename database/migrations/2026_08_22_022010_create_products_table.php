<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->string('status')
                ->default('draft')
                ->index();

            $table->boolean('is_featured')
                ->default(false)
                ->index();

            $table->unsignedSmallInteger('warranty_months')
                ->nullable();

            $table->decimal('weight', 10, 3)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'category_id',
                'status',
                'is_featured',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
