<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Sequence;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Generates exactly 30 structured members
        Member::factory()->count(30)->sequence(fn (Sequence $sequence) => [
            'membership_no' => 'GYM-' . str_pad($sequence->index + 1, 4, '0', STR_PAD_LEFT),
        ])
        ->create();
    }
}
