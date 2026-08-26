<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
        //  Force the entry method to lowercase to prevent casing mismatches
        $entryMethod = strtolower(trim($this->entry_method));

        //  Sanitize specific fields based on the chosen entry method
        $membershipNo = null;
        $walkinName = null;

        if (in_array($entryMethod, ['qr_scan', 'manual_member'])) {
            // Trim spaces from membership number. 
            // If it's a full QR URL payload, y extract the ID h
            $membershipNo = $this->membership_no ? trim($this->membership_no) : null;
        }

        if ($entryMethod === 'manual_walkin') {
            // Clean up the name string (strip extra inside spaces and capitalize words)
            $walkinName = $this->walkin_name 
                ? ucwords(strtolower(preg_replace('/\s+/', ' ', trim($this->walkin_name)))) 
                : null;
        }

         // Overwrite the request payload with cleaned and filtered values
        $this->merge([
            'entry_method' => $entryMethod,
            'membership_no' => $membershipNo, // Will be null if entry_method is walk-in
            'walkin_name' => $walkinName,     // Will be null if entry_method is member/QR
        ]);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entry_method' => [
                'required', 
                Rule::in(['qr_scan', 'manual_member', 'manual_walkin'])
            ],
            'membership_no' => [
                'required_if:entry_method,qr_scan,manual_member', 
                'nullable', 
                'string'
            ],
            'walkin_name' => [
                'required_if:entry_method,manual_walkin', 
                'nullable', 
                'string', 
                'min:3',
                'max:255'
            ],
        ];
    }
}
