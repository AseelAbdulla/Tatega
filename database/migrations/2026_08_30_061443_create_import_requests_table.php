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
        Schema::create('import_requests', function (Blueprint $table) {
            $table->id();

            // العميل صاحب الطلب
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // حالة الطلب: معلق، تم إرسال العرض، مقبول، مرفوض، ملغى
            $table->enum('status', ['pending', 'offer_sent', 'approved', 'rejected', 'cancelled', 'offer_accepted'])
                ->default('pending');

            // بيانات التواصل والعنوان المدخلة من العميل
            $table->string('currency', 10);
            $table->string('phone', 20);
            $table->text('address_details');
            $table->text('notes')->nullable();

            // رد الإدارة والتسعير (تكون خالية عند تقديم الطلب وتعبأ عند مراجعة المدير)
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('offered_shipping_fee', 12, 2)->nullable();
            $table->decimal('offered_items_total', 12, 2)->nullable();
            $table->decimal('offered_grand_total', 12, 2)->nullable();
            $table->timestamp('offer_expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_requests');
    }
};
