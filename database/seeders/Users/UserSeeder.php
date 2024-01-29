<?php

namespace Database\Seeders\Users;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentHead = User::query()->create([
            'name' => 'Dədə qorqud',
            'surname' => 'Canıyev',
            'father_name' => 'Təpəgöz',
            'phone' => '+994555555555',
            'email' => 'dedeqorqud@gmail.com',
            'email_verified_at' => Carbon::now(),
            'username' => 'dedeqorqud',
            'birth_date' => '1969-04-10 23:59:00',
            'education' => 'FULL',
            'education_files' => null,
            'certificate_files' => null,
            'cv_files' => null,
            'self_photo_files' => null,
            'previous_job' => 'Qırat MMC-də nalvuranın nalvurançısının nalvurançısı',
            'account_status' => 'APPROVED',
            'password' => Hash::make('123456789')
        ]);

        $leadingExpert = User::query()->create([
            'name' => 'Alı kişi',
            'surname' => 'Canıyev',
            'father_name' => 'Dədə qorqud',
            'phone' => '+994556666666',
            'email' => 'alikisi@gmail.com',
            'email_verified_at' => Carbon::now(),
            'username' => 'alikisi',
            'birth_date' => '1978-09-30 23:59:00',
            'education' => 'FULL',
            'education_files' => null,
            'certificate_files' => null,
            'cv_files' => null,
            'self_photo_files' => null,
            'previous_job' => 'Qırat MMC-də nalvuran',
            'account_status' => 'APPROVED',
            'password' => Hash::make('123456789')
        ]);

        $accountant = User::query()->create([
            'name' => 'Koroğlu',
            'surname' => 'Canıyev',
            'father_name' => 'Alı',
            'phone' => '+994557777777',
            'email' => 'koroglu@gmail.com',
            'email_verified_at' => Carbon::now(),
            'username' => 'koroglu',
            'birth_date' => '1988-09-30 23:59:00',
            'education' => 'FULL',
            'education_files' => null,
            'certificate_files' => null,
            'cv_files' => null,
            'self_photo_files' => null,
            'previous_job' => 'Qırat MMC-də nalvuranın nalvurançısı',
            'account_status' => 'APPROVED',
            'password' => Hash::make('123456789')
        ]);

        $departmentHead->assignRole('department_head');
        $leadingExpert->assignRole('leading_expert');
        $accountant->assignRole('accountant');

        $companyEmployee = Employee::query()->create([
            'name' => 'Bamsı',
            'surname' => 'Beyrək',
            'father_name' => 'Dədə qorqud',
            'company_id' => 1,
            'birth_date' => '1999-09-30',
            'id_card_serial' => 'AZE8888888',
            'fin_code' => '4RYESJV',
            'id_card_date' => '2019-09-30',
            'ssn' => 1234567890123,
            'start_date_of_employment' => '2021-09-30',
            'end_date_of_employment' => null,
            'previous_job' => 'Qırat MMC-də at sürücüsü',
            'phone' => '+994501234567',
            'email' => 'bamsibeyrek@gmail.com',
            'education' => 'FULL',
            'salary' => 2500,
            'password' => Hash::make('123456789')
        ]);
    }
}
