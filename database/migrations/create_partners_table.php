<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {

            $table->id();

            $table->json('name');

            $table->string('logo', 255);

            $table->string('website_url', 255)->nullable();

            $table->integer('sort_order')->default(0);

            $table->string('status', 50)->default('active');

            $table->decimal('lat', 10, 8)->nullable();

            $table->decimal('lng', 11, 8)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
