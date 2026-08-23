<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('international_import_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('request_number')->unique();

            $table->string('title');

            $table->string('country');

            $table->decimal('price', 12, 2)->nullable();

            $table->text('description')->nullable();
            

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'shipping',
                'delivered',
            ])->default('pending');

            $table->text('admin_note')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->string('tracking_number')->nullable();

            $table->date('estimated_delivery')->nullable();

            $table->date('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('international_import_requests');
    }
};
