<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Member;

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
            //ucfirst for proper casing of text example "Zai Santos"
            'first_name' => ucwords(strtolower(trim($this->first_name))),
            'last_name'  => ucwords(strtolower(trim($this->last_name))),
            'contact_number' => $this->normalizePhPhoneNumber($this->contact_number),
            'emergency_contact_number' => $this->normalizePhPhoneNumber($this->emergency_contact_number),  
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
        $member = $this->route('member'); // Get the member instance from the route, if it exists
        $memberId = is_object($member) ? $member->id : $member; // If it's an object, get the ID, otherwise use it directly  

        // This ensures that even if they bypass the frontend, the backend catches it
        //matches the regex pattern in the frontend for ph phone numbers
        $phPhoneRegex = 'regex:/^09\d{9}$/';

        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'contact_number' => [
                'required', 
                'string', 
                $phPhoneRegex, 
                Rule::unique('members', 'contact_number')->ignore($memberId),
            ],
            'emergency_contact_number' => [
                'required', 
                'string', 
                $phPhoneRegex
            ],
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'photo' => 'nullable|mimes:jpg,jpeg,png,webp,heic|max:5120' 
        ];
    }

    private function normalizePhPhoneNumber($number): ?string
    {
        if (!$number) return null;

        //strip out anything that isn't a digit
        $cleaned = preg_replace('/\D/', '', $number);

        // Matches 12 digits starting with 639 (e.g., 639171234567)
        if (preg_match('/^639\d{9}$/', $cleaned)) {
            return '0' . substr($cleaned, 2);
        }

        // Matches 11 digits starting with 09 (e.g., 09171234567)
        if (preg_match('/^09\d{9}$/', $cleaned)) {
            return $cleaned;
        }

        // Matches 10 digits starting with 9 (e.g., 9171234567)
        if (preg_match('/^9\d{9}$/', $cleaned)) {
            return '0' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Configure the validator instance.
     * Intercepts "Soft Duplicates" based on name and DOB combinations.
     */

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            //run only the validation if it is a create requesst
            if ($this->isMethod('post')) {

                $firstName = trim($this->first_name);
                $lastName = trim($this->last_name);

                // Only perform this check for new registrations (store), not updates
            
               $duplicateExists = Member::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                ->where('date_of_birth', '=', $this->date_of_birth)
                ->exists();

                if ($duplicateExists) {
                    // Fail the whole validation block cleanly
                    $validator->errors()->add(
                        'first_name', 
                        'A member with this exact name and date of birth is already registered.'
                    );
                }   
            }
        });
    }
}
