<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PrayerReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Admin User
        User::create([
            'email'    => 'admin@celz5.org',
            'password' => Hash::make('password123'),
        ]);

        // 2. Define Groups and Churches
        $groups = [
            'CE LEKKI GROUP' => ['PEARL GROUP', 'HAVEN OF GRACE', 'LIGHT HOUSE'],
            'CE TEDO GROUP'  => ['CE TEDO', 'CE FIDISO', 'CE MIRACLE AVENUE'],
            'CE AJAH GROUP'  => ['CE ADDO ROAD', 'CE BERGER ROAD', 'CE AJAH SUNRISE'],
        ];

        // 3. Generate 50 Sample Reports
        for ($i = 0; $i < 50; $i++) {
            $groupName = array_rand($groups);
            $churchName = $groups[$groupName][array_rand($groups[$groupName])];
            $attendanceCount = rand(10, 150); // Generate a random number for attendance

            PrayerReport::create([
                'group'        => $groupName,
                'church'       => $churchName,
                'prayer_link'  => 'https://kingsconference.org/meeting/' . strtolower(str_replace(' ', '', $churchName)),
                'meeting_date' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                'attendance'   => $attendanceCount, // Added this to fix the Integrity Constraint Violation
                'testimony'    => 'Glory to God! We had a powerful session with ' . $attendanceCount . ' souls in attendance.',
            ]);
        }
    }
}
