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
        // Generate start dates: half in the past, half recently/today
        $startDate = fake()->dateTimeBetween('-1 month', 'now');
        $endDate = (clone $startDate)->modify('+30 days');

        // A member is only truly active if their end date hasn't passed 
        // AND they haven't been manually deactivated/flagged
        $isNotExpired = $endDate >= new \DateTime();
        $isActive = $isNotExpired && fake()->boolean(85); 

        return [
            'first_name'               => fake()->firstName(),
            'last_name'                => fake()->lastName(),
            'contact_number'           => fake()->phoneNumber(),
            'email'                    => fake()->unique()->safeEmail(), 
            'emergency_contact_number' => fake()->phoneNumber(),
            'address'                  => fake()->address(),
            'gender'                   => fake()->randomElement(['Male', 'Female', 'Other']),
            'date_of_birth'            => fake()->date('Y-m-d', '-18 years'),
            'photo_path'               => null,
            'is_active'                => $isActive,
            'membership_start'         => $startDate,
            'membership_end'           => $endDate,
            'last_renewal_at'          => $isNotExpired ? fake()->optional(0.4)->dateTimeBetween($startDate, 'now') : null,
        ];
    }
}
