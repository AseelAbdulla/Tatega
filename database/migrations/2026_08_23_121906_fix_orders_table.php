<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Fix payment receipt column name
            |--------------------------------------------------------------------------
            */

            if (Schema::hasColumn('orders', 'payment_recepit')
                && !Schema::hasColumn('orders', 'payment_receipt')) {

                $table->renameColumn(
                    'payment_recepit',
                    'payment_receipt'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Make customer information nullable
            |--------------------------------------------------------------------------
            */

            $table->string('customer_name', 255)
                ->nullable()
                ->change();

            $table->string('customer_phone', 50)
                ->nullable()
                ->change();

            $table->string('customer_email', 255)
                ->nullable()
                ->change();

            /*
            |--------------------------------------------------------------------------
            | Make shipping information nullable
            |--------------------------------------------------------------------------
            */

            $table->string('shipping_country', 100)
                ->nullable()
                ->change();

            $table->string('shipping_city', 100)
                ->nullable()
                ->change();

            $table->string('shipping_region', 100)
                ->nullable()
                ->change();

            $table->string('shipping_street', 255)
                ->nullable()
                ->change();

            $table->string('shipping_building', 100)
                ->nullable()
                ->change();

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            $table->string('payment_method', 50)
                ->nullable()
                ->change();

            $table->string('payment_status', 30)
                ->nullable()
                ->change();

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 12, 2)
                ->default(0)
                ->change();

            $table->decimal('discount', 12, 2)
                ->default(0)
                ->change();

            $table->decimal('tax', 12, 2)
                ->default(0)
                ->change();

            $table->decimal('shipping_fee', 12, 2)
                ->default(0)
                ->change();

            $table->decimal('total_price', 12, 2)
                ->default(0)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (Schema::hasColumn('orders', 'payment_receipt')
                && !Schema::hasColumn('orders', 'payment_recepit')) {

                $table->renameColumn(
                    'payment_receipt',
                    'payment_recepit'
                );
            }
        });
    }
};