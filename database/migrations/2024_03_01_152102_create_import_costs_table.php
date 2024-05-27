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
        Schema::create('import_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_query_id');
            $table->unsignedBigInteger('company_id');
            $table->float('total_ratio')->default(100);
            $table->float('total_amount')->default(0);
            $table->float('total_short_import_duty')->default(0);
            $table->float('total_customs_short_and_import_duty')->default(0);
            $table->float('total_other_expenses')->default(0);
            $table->float('total_customs_collection')->default(0);
            $table->float('total_transport_expenses')->default(0);
            $table->float('total_import_fee_and_other_expenses')->default(0);
            $table->float('total_vat')->default(0);
            $table->float('total_amount_azn')->default(0);
            $table->timestamps();

            $table->foreign('import_query_id')->references('id')
                ->on('import_queries')->onDelete('cascade');
            $table->foreign('company_id')->references('id')
                ->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_costs');
    }
};
