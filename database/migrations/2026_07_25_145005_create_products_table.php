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

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->json('name');
            $table->json('description')->nullable();

            $table->string('sku', 100)->unique();

            $table->decimal('base_price', 10, 2);
            $table->boolean('has_discount')->default(false);
            $table->decimal('discount_price', 10, 2)->nullable();

            $table->integer('stock');
            $table->integer('low_stock_threshold')->default(5);

            $table->string('status', 50)->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
