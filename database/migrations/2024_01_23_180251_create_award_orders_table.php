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
        Schema::create('award_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('tax_id_number')->nullable();
            $table->longText('main_part_of_order')->nullable();
            $table->date('order_date')->nullable();
            $table->string('d_name')->nullable();
            $table->string('d_surname')->nullable();
            $table->string('d_father_name')->nullable();
            $table->jsonb('generated_file')->nullable();
            $table->jsonb('worker_infos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_orders');
    }
};
