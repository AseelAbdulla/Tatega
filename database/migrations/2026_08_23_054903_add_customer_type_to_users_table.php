<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة نوع العميل إلى جدول users
     *
     * local
     * international
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('customer_type', 30)
                ->default('local')
                ->after('phone');

        });
    }

    /**
     * التراجع عن التعديل
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('customer_type');

        });
    }
};
