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

         //cleaning the emails table before seeding
        $cleaned_users = array_map(function($item){

            $cleanEmail = strtolower(trim($item['email']));
            $cleanEmail = filter_var($cleanEmail, FILTER_SANITIZE_EMAIL);
            $cleanFirstName = trim($item['first_name']);
            $cleanLastName = trim($item['last_name']);

            return [
                'email' => $cleanEmail,
                'first_name' => $cleanFirstName,
                'last_name' => $cleanLastName,
                'organization_id' => $item['organization_id'],
                'role_id' => $item['role_id'],
                'is_active' => $item['is_active'],
            ];

        }, $users);

        foreach ($cleaned_users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], // where condition
                [
                    'organization_id' => $user['organization_id'],
                    'role_id' => $user['role_id'],
                    'is_active' => $user['is_active'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
