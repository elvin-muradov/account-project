<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentHeadRole = Role::query()->create([
            'name' => 'department_head',
            'display_name_az' => 'Şöbə müdiri',
            'display_name_en' => 'Department head',
            'display_name_ru' => 'Начальник отдела',
        ]);

        $accountantRole = Role::query()->create([
            'name' => 'accountant',
            'display_name_az' => 'Mühasib',
            'display_name_en' => 'Accountant',
            'display_name_ru' => 'Бухгалтер',
        ]);

        $leadingExpertRole = Role::query()->create([
            'name' => 'leading_expert',
            'display_name_az' => 'Aparıcı mütəxəssis',
            'display_name_en' => 'Leading expert',
            'display_name_ru' => 'Ведущий эксперт',
        ]);
    }
}
