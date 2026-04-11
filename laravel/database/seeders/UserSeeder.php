<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $adminRole = DB::table('roles')->where('slug', 'admin')->value('id');
        $staffRole = DB::table('roles')->where('slug', 'staff')->value('id');

        $users = [

            //admin
            [
                'first_name'=> 'Gym', 
                'last_name'=> 'Owner',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id'=> $adminRole
            ],

            //staff
            [
                'first_name'=> 'Gym', 
                'last_name'=> 'Staff',
                'email' => 'staff@example.com',
                'password' => Hash::make('password123'),
                'role_id'=> $staffRole
            ],
        ];
    }
}
