<?php

use App\Enums\TransactionTypesEnum;
use App\Enums\TreasureTypesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treasure_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type_of_treasure', TreasureTypesEnum::toArray());
            $table->enum('type_of_transaction', TransactionTypesEnum::toArray());
            $table->string('invoice_number')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount');
            $table->decimal('treasure_balance')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->dateTime('contract_date')->default(now());
            $table->text('destination'); // Təyinat
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('treasure_id');
            $table->unsignedBigInteger('currency_id')->nullable(); // Valyuta
            $table->unsignedBigInteger('treasure_transaction_id')->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')
                ->onDelete('cascade');
            $table->foreign('treasure_id')->references('id')->on('treasures')
                ->onDelete('cascade');
            $table->foreign('currency_id')->nullOnDelete()
                ->references('id')->on('currencies');
            $table->foreign('treasure_transaction_id')->nullOnDelete()
                ->references('id')->on('treasure_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasure_transactions');
    }
};
