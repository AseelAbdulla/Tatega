<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {

            $table->id();

            // العميل صاحب طريقة الدفع
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // نوع طريقة الدفع
            // card / cash / bank_transfer ...
            $table->string('type', 50);

            // اسم صاحب البطاقة
            $table->string('cardholder_name')
                ->nullable();

            // آخر 4 أرقام فقط
            $table->string('last_four', 4)
                ->nullable();

            // الشهر
            $table->unsignedTinyInteger('expiry_month')
                ->nullable();

            // السنة
            $table->unsignedSmallInteger('expiry_year')
                ->nullable();

            // هل هي طريقة الدفع الافتراضية؟
            $table->boolean('is_default')
                ->default(false);

            // حالة الطريقة
            $table->string('status', 30)
                ->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
