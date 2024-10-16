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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['COMPANY', 'EMPLOYEE']);
            $table->enum('subtype', ['ASAN_SIGN', 'YDM_CARD', 'SALARY_CARD', 'OTHER'])
                ->default('OTHER');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('accountant_id');
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')
                ->on('companies')->onDelete('cascade');
            $table->foreign('employee_id')->nullOnDelete()
                ->references('id')->on('employees')
                ->onDelete('cascade');
            $table->foreign('accountant_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
