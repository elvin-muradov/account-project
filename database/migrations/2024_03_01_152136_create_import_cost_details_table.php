<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_cost_id');
            $table->unsignedBigInteger('import_query_detail_id');
            $table->float('ratio')->default(0);
            $table->float('short_import_duty')->default(0);
            $table->float('customs_short_and_import_duty')->default(0);
            $table->float('other_expenses')->default(0);
            $table->float('customs_collection')->default(0);
            $table->float('transport_expenses')->default(0);
            $table->float('import_fee_and_other_expenses')->default(0);
            $table->float('vat')->default(0);
            $table->float('subtotal_amount_azn')->default(0);
            $table->float('price_per_unit_of_measure_azn')->default(0);
            $table->float('quantity')->default(0);
            $table->timestamps();

            $table->foreign('import_cost_id')->references('id')->on('import_costs')
                ->onDelete('cascade');
            $table->foreign('import_query_detail_id')->references('id')->on('import_query_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_cost_details');
    }
};
