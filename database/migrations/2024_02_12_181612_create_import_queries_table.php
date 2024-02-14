<?php

use App\Enums\ImportPaymentStatuses;
use App\Enums\TransportTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('query_number')->unique();
            $table->string('customs_barcode')->unique();
            $table->string('shipping_from');
            $table->string('seller_company_name');
            $table->date('delivery_date');
            $table->date('customs_date');
            $table->float('invoice_value');
            $table->float('customs_value');
            $table->float('statistic_value');
            $table->float('net_weight');
            $table->unsignedBigInteger('currency_id');
            $table->enum('transport_type', TransportTypes::toArray());
            $table->enum('payment_status', ImportPaymentStatuses::toArray());

            //Customs fee/duty
            $table->float('customs_transaction_fee')->default(0); // 2
            $table->float('customs_transaction_fee_24_hours')->default(0); // 19
            $table->float('import_fee')->default(0); // 20
            $table->float('vat')->default(0); // 32
            $table->float('electronic_customs_fee')->default(0); // 75
            $table->float('vat_for_electronic_customs_fee')->default(0); // 85
            $table->timestamps();

            $table->foreign('currency_id')->references('id')
                ->on('currencies');
            $table->foreign('company_id')->references('id')
                ->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_queries');
    }
};
