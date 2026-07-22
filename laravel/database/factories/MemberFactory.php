<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', 'now');
        // Clone the start date and add 30 days for the end date logic
        $endDate = (clone $startDate)->modify('+30 days');

        return [
            // 'membership_no' is removed here so the Model's static::created hook takes over
            'first_name'               => fake()->firstName(),
            'last_name'                => fake()->lastName(),
            'contact_number'           => fake()->phoneNumber(),
            'emergency_contact_number' => fake()->phoneNumber(),
            'address'                  => fake()->address(),
            'gender'                   => fake()->randomElement(['Male', 'Female', 'Other']),
            'date_of_birth'            => fake()->date('Y-m-d', '-18 years'),
            'photo_path'               => null,
            'is_active'                => fake()->boolean(80),
            'membership_start'         => $startDate,
            'membership_end'           => $endDate,
            'last_renewal_at'          => fake()->optional(0.5)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
