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
            //added null safe operator for handling empty fields properly
            'first_name' => $this->first_name ? ucwords(strtolower(trim($this->first_name))) : null,
            'last_name'  => $this->last_name ? ucwords(strtolower(trim($this->last_name))) : null,
            'contact_number' => $this->contact_number ? $this->normalizePhPhoneNumber($this->contact_number) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'emergency_contact_number' => $this->emergency_contact_number ? $this->normalizePhPhoneNumber($this->emergency_contact_number) : null,  
            'address' => $this->address ? ucfirst(trim($this->address)) : null,
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
            'email' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                    
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

    /**
     * Configure the validator instance.
     * Intercepts "Soft Duplicates" based on name and DOB combinations.
     */

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            // Only validate duplicates on fresh creation, and ONLY if bypass isn't requested
            if ($this->isMethod('post') && !$this->boolean('bypass_duplicate_check')) {

                // Safely grab the already-normalized values from the request
                $firstName = $this->input('first_name');
                $lastName = $this->input('last_name');

                //sql query for checking the dupicates
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

  
}
