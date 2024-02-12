<?php

use App\Enums\EducationTypesEnum;
use App\Enums\StatusTypesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('father_name');
            $table->string('username');
            $table->string('phone');
            $table->date('birth_date');
            $table->enum('education', EducationTypesEnum::toArray());
            $table->jsonb('education_files')->nullable();
            $table->jsonb('certificate_files')->nullable();
            $table->jsonb('cv_files')->nullable();
            $table->jsonb('self_photo_files')->nullable();
            $table->text('previous_job')->nullable();
            $table->enum('account_status', StatusTypesEnum::toArray())->default('PENDING');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->dateTime('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
