<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Evaluate expiry right here to ensure the API payload is 100% accurate
        $isExpired = $this->expiration_date && Carbon::parse($this->expiration_date)->isPast();

        return [
            'id' => $this->id,
            'age' => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null,
            'membership_no' => $this->membership_no, // Handled by your Model boot
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}"),
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'emergency_contact_number' => $this->emergency_contact_number,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            // If expired, force false, otherwise use the database value
            'is_active' => $isExpired ? false : (bool)$this->is_active,
            'membership_start' => $this->membership_start ? $this->membership_start->format('F j, Y - g:i A') : null,
            'membership_end' => $this->membership_end ? $this->membership_end->format('F j, Y - g:i A') : null,
            'can_renew' => $this->can_renew, // Accesses model attribute/getter
            
            // Seamlessly compute public URLs for images
            'photo_url' => $this->photo_path ? asset('storage/' . $this->photo_path) : null,

            'qr_token' => $this->qr_token,
            'qr_code_url' => route('api.members.getQrCode', $this->id), // if using a dedicated endpoint route
        ];
    
    }
}
