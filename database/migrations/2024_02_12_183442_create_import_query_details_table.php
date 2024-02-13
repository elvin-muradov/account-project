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
            $table->string('material_title');
            $table->string('material_group_title');
            $table->string('measure');
            $table->float('price_per_unit_of_measure');
            $table->float('subtotal_amount');
            $table->timestamps();
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
