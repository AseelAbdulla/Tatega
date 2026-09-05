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
        Schema::disableForeignKeyConstraints();
        Schema::create('import_request_items', function (Blueprint $table) {
            $table->id();
            
            // الربط بجدول الطلبات الرئيسي
            $table->foreignId('import_request_id')->constrained('import_requests')->onDelete('cascade');
            
            // الربط بالمنتج والوحدة
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('product_units')->onDelete('cascade');
            
            // الكمية المطلوبة من العميل
            $table->integer('quantity');
            
            // أسعار المدير المحددة بعد المراجعة (nullable حتى يقبل المدير الطلب)
            $table->decimal('offered_unit_price', 12, 2)->nullable();
            $table->decimal('offered_subtotal', 12, 2)->nullable();
            
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_request_items');
    }
};
