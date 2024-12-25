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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fixed_asset_category_id');
            $table->float('initial_value');
            $table->float('percentage');
            $table->integer('useful_life_years');
            $table->enum('type', ['vehicle', 'other']);
            $table->date('start_of_using'); // İstifadəyə alındığı tarix
            $table->date('end_of_using')->nullable(); // Son tarix
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')
                ->onDelete('cascade');
            $table->foreign('fixed_asset_category_id')->references('id')
                ->on('fixed_asset_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
