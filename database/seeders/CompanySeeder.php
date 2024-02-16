<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i < 11; $i++) {
            Company::query()->create([
                'company_name' => "Company $i Hüquqi",
                'company_short_name' => "CYH$i",
                'company_category' => 'MICRO',
                'company_obligation' => 'SIMPLIFIED',
                'company_address' => 'Baku, Azerbaijan',
                'company_emails' => ['company1@mail.ru', 'company2@mail.ru'],
                'owner_type' => 'LEGAL',
                'tax_id_number' => 1234567890,
                'tax_id_number_date' => '2021-01-01',
                'dsmf_number' => 1234567890123,
                'main_user_id' => null,
                'director_id' => null,
                'asan_sign' => '+994501234567',
                'asan_sign_start_date' => '2021-01-01',
                'birth_id' => '2021-01-01',
                'pin1' => 1234,
                'pin2' => 12345,
                'puk' => 12345678,
                'statistic_code' => 12345678,
                'statistic_password' => '12345678',
                'operator_azercell_account' => '+994501234567',
                'operator_azercell_password' => '12345678'
            ]);
        }
        for ($i = 1; $i < 30; $i++) {
            Company::query()->create([
                'company_name' => "Company $i Fiziki",
                'company_short_name' => "CYF$i",
                'company_category' => 'MICRO',
                'company_obligation' => 'SIMPLIFIED',
                'company_address' => 'Baku, Azerbaijan',
                'company_emails' => ['company1@mail.ru', 'company2@mail.ru'],
                'owner_type' => 'INDIVIDUAL',
                'tax_id_number' => 1234567890,
                'tax_id_number_date' => '2021-01-01',
                'dsmf_number' => 1234567890123,
                'main_user_id' => null,
                'director_id' => null,
                'asan_sign' => '+994501234567',
                'asan_sign_start_date' => '2021-01-01',
                'birth_id' => '2021-01-01',
                'pin1' => 1234,
                'pin2' => 12345,
                'puk' => 12345678,
                'statistic_code' => 12345678,
                'statistic_password' => '12345678',
                'operator_azercell_account' => '+994501234567',
                'operator_azercell_password' => '12345678'
            ]);
        }
    }
}
