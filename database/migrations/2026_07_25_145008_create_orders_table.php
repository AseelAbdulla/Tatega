<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('order_type', 50)->default('normal');
            $table->string('status', 50)->default('pending');

            $table->string('customer_name', 255);
            $table->string('customer_phone', 50);
            $table->string('customer_email', 255);

            $table->foreignId('address_id')
                  ->nullable()
                  ->constrained('addresses')
                  ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
