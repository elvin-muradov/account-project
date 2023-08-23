<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Carbon\Carbon;
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

        $educationFiles = json_encode(['education1.pdf','education2.pdf']);
        $certificates = json_encode(['certificate1.pdf', 'certificate2.pdf', 'certificate3.pdf']);
        $profilePP = json_encode('pp.png');

        User::query()->create([
            'name' => 'Elvin',
            'surname' => 'Muradov',
            'father_name' => 'Amil',
            'phone' => '+994559948808',
            'email' => 'mrdvelvin@gmail.com',
            'email_verified_at' => Carbon::now(),
            'username' => 'mrdvelvin',
            'birth_date' => '1998-03-20 23:59:00',
            'education' => 'FULL',
            'education_files' => $educationFiles,
            'certificate_files' => $certificates,
            'cv_file' => null,
            'self_photo_file' => $profilePP,
            'previous_job' => 'FANFI MMC',
            'account_status' => 'APPROVED',
            'password' => Hash::make('123456789')
        ]);
    }
}
