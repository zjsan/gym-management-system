<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GymSetting;


class GymSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        GymSetting::firstOrCreate(
            ['key' => 'walkin_daily_fee'],
            ['value' => 100.00, 'description' => 'Daily workout pass fee for walk-in guests']
        );

        GymSetting::firstOrCreate(
            ['key' => 'monthly_membership_fee'],
            ['value' => 1200.00, 'description' => 'Regular monthly membership registration and renewal rate']
        );
        
    }
}
