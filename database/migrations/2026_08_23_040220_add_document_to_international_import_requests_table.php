<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('international_import_requests', function (Blueprint $table) {
            $table->string('document_path')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('international_import_requests', function (Blueprint $table) {
            $table->dropColumn('document_path');
        });
    }
};
