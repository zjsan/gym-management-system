<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membership_no' => $this->membership_no, // Handled by your Model boot
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}"),
            'contact_number' => $this->contact_number,
            'emergency_contact_number' => $this->emergency_contact_number,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'is_active' => (bool) $this->is_active,
            'membership_start' => $this->membership_start ? $this->membership_start->toIso8601String() : null,
            'membership_end' => $this->membership_end ? $this->membership_end->toIso8601String() : null,
            'can_renew' => $this->can_renew, // Accesses model attribute/getter
            
            // Seamlessly compute public URLs for images
            'photo_url' => $this->photo_url,
        ];
    
    }
}
