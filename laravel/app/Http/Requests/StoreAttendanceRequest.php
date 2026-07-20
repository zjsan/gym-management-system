<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
                'max:255'
            ],
        ];
    }
}
