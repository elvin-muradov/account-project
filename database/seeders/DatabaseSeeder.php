<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Users\RolePermissionSeeder;
use Database\Seeders\Users\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call(RolePermissionSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(UserSeeder::class);
    }
}
