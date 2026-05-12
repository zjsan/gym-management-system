<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_create_member_with_photo()
    {
        Storage::fake('public'); // Intercepts file saving so you don't clutter your PC
        
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_number' => '09123456789',
            'emergency_contact_number' => '09987654321',
            'address' => '123 Gym St.',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'photo' => UploadedFile::fake()->image('avatar.jpg')
        ];

        $response = $this->postJson('/api/members', $data);

        $response->assertStatus(201); // Created
        $this->assertDatabaseHas('members', ['first_name' => 'John']);
        
        // Check if the file was actually saved
        $member = Member::first();
        Storage::disk('public')->assertExists($member->photo_path);
    }
}
