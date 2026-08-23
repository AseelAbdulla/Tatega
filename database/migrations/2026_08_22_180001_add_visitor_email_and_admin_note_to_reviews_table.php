<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('visitor_email', 255)
                ->nullable()
                ->after('visitor_name');

            $table->text('admin_note')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'visitor_email',
                'admin_note',
            ]);
        });
    }
};