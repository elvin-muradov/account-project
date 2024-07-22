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
        Schema::create('envelopes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('from_company_id')->nullable();
            $table->string('to_company_name')->nullable();
            $table->unsignedBigInteger('to_company_id')->nullable();
            $table->string('from_company_name')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->jsonb('envelopes')->nullable();
            $table->dateTime('sent_at');
            $table->enum('type', ['OUTGOING', 'INCOMING']);
            $table->timestamps();

            $table->foreign('from_company_id')->nullOnDelete()->references('id')
                ->on('companies')->onDelete('cascade');
            $table->foreign('to_company_id')->nullOnDelete()
                ->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envelopes');
    }
};
