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
        Schema::create('import_query_details', function (Blueprint $table) {
            $table->id();
            $table->string('material_title_local');
            $table->string('material_barcode');
            $table->string('material_title_az');
            $table->string('measure');
            $table->unsignedBigInteger('measure_id')->nullable();
            $table->float('quantity');
            $table->float('price_per_unit_of_measure');
            $table->float('subtotal_amount');
            $table->unsignedBigInteger('import_query_id');
            $table->timestamps();

            $table->foreign('import_query_id')->references('id')
                ->on('import_queries')->onDelete('cascade');
            $table->foreign('measure_id')
                ->nullOnDelete()
                ->references('id')
                ->on('import_queries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_query_details');
    }
};
