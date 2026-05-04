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
        return Gate::allows('access-front-desk');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => ucfirst(trim($this->first_name)),
            'last_name'  => ucfirst(trim($this->last_name)),
            'contact_number' => trim($this->contact_number),
            
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        $userId = $this->route('user')?->id; // Get the ID only if the route parameter 'user' exists

        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'contact_number' => 'required|string|max:11|unique:members,contact_number,' . $userId,
            'photo' => 'image|max:2048' // Ensure image upload
        ];
    }
}
