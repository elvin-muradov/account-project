<?php

use App\Enums\RentalTypes;
use App\Enums\UserTypesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('object_name'); // Obyektin adı
            $table->string('object_code')->nullable(); // Obyektin kodu
            $table->unsignedBigInteger('company_id'); // Şirkət
            $table->unsignedBigInteger('creator_id'); // Yaradan
            $table->date('start_date'); // Başlama tarixi
            $table->date('end_date'); // Bitmə tarixi
            $table->string('rental_area'); // Kirayə sahəsi
            $table->string('rental_price'); // Məbləğ
            $table->string('rental_price_with_vat')->nullable(); // Məbləğ (ƏDV ilə)
            $table->boolean('is_vat')->default(false); // ƏDV var?
            $table->enum('tenant_type', UserTypesEnum::toArray()); // Kirayəçi növü
            $table->jsonb('contract_files')->nullable(); // Müqavilə faylları
            $table->text('address')->nullable();
            $table->enum('type', RentalTypes::toArray()); // Obyektin növü (SHOP, WAREHOUSE, VEHICLE)
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_contracts');
    }
};
