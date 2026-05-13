<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => ucfirst(trim($this->first_name)),
            'last_name'  => ucfirst(trim($this->last_name)),
            'contact_number' => trim($this->contact_number),
            'emergency_contact_number' => trim($this->emergency_contact_number),    
            'address' => ucfirst(trim($this->address)),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        $memberId = $this->route('member')?->id; // Get the ID only if the route parameter 'user' exists

        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'contact_number' => 'required|string|max:11|unique:members,contact_number,' . $memberId,
            'emergency_contact_number' => 'required|string|max:11',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'photo' => 'nullable|image|max:2048' // Ensure image upload
        ];
    }
}
