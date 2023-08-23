<?php

use App\Enums\CompanyCategoriesEnum;
use App\Enums\CompanyObligationsEnum;
use App\Enums\UserTypesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name'); // Şirkətin adı
            $table->enum('company_category', CompanyCategoriesEnum::toArray()); // Şirkət kateqoriyası
            $table->enum('company_obligation', CompanyObligationsEnum::toArray()); // Şirkət mükəlləfiyyəti
            $table->enum('owner_type', UserTypesEnum::toArray()); // Fiziki yaxud Hüquqi
            $table->jsonb('company_emails'); // Şirkət e-poçtları
            $table->unsignedBigInteger('voen'); // VÖEN
            $table->date('voen_date'); // VÖEN alınma tarixi
            $table->unsignedBigInteger('dsmf_number'); // DSMF üçot nömrəsi
            $table->jsonb('charter_file'); // Nizamnamə faylı
            $table->jsonb('extract_file'); // Çıxarış faylı
            $table->unsignedBigInteger('main_user_id'); // Səlahiyyətli şəxs
            $table->jsonb('director_id_card_file'); // Direktorun ŞV faylı
            $table->jsonb('creators_files');
            $table->string('asan_sign'); // ASAN imza mobil nömrəsi
            $table->date('asan_sign_start_date'); // ASAN imza başlama vaxtı
            $table->date('birth_id'); // İD doğum tarixi
            $table->unsignedInteger('pin1'); // PİN 1
            $table->unsignedInteger('pin2'); // PİN 2
            $table->unsignedInteger('puk'); // PUK
            $table->unsignedInteger('statistic_code'); // Statistika kodu
            $table->string('statistic_password'); // Statistika şifrəsi
            $table->string('operator_azercell_account'); // Operator kabineti hesabı (Azercell)
            $table->string('operator_azercell_password'); // Operator kabineti parolu
            $table->string('ydm_account_email')->nullable(); // YDM hesabı elektron poçtu
            $table->string('ydm_password')->nullable(); // YDM hesabı şifrəsi
            $table->date('ydm_card_expired_at')->nullable(); // YDM kartının bitiş tarixi

            $table->timestamps();

            $table->foreign('main_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
